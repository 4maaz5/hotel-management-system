<?php

namespace App\Services\Chatbot;

use App\Models\AiChatLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Collection;
use RuntimeException;

class AiChatService
{
    public function __construct(
        private readonly KnowledgeBaseService $knowledgeBaseService,
        private readonly LanguageDetector $languageDetector,
        private readonly IntentDetectionService $intentDetectionService,
        private readonly ToolExecutorService $toolExecutorService,
        private readonly OpenAiResponsesClient $client,
    ) {
    }

    public function process(ChatSession $session, ChatMessage $userMessage): ChatMessage
    {
        $language = $this->languageDetector->detect($userMessage->message, $session->language ?: app()->getLocale());
        $history = $this->history($session, $userMessage);
        $knowledge = $this->knowledgeBaseService->search($userMessage->message, $language);

        $log = AiChatLog::create([
            'session_id' => $session->id,
            'message_id' => $userMessage->id,
            'user_id' => $session->user_id,
            'language' => $language,
            'status' => 'processing',
            'request_payload' => [
                'message' => $userMessage->message,
                'history' => $history->map(fn (ChatMessage $message) => [
                    'role' => $message->role,
                    'message' => $message->message,
                ])->all(),
                'knowledge' => $knowledge,
            ],
        ]);

        try {
            $plan = $this->intentDetectionService->detect($session, $history, $userMessage, $knowledge, $language);
            $toolResult = $this->toolExecutorService->execute($session, $plan, $userMessage->message);
            $assistantText = $this->composeAssistantText($language, $plan, $toolResult, $knowledge);

            $assistantMessage = $session->messages()->create([
                'role' => 'assistant',
                'message' => $assistantText,
                'metadata' => [
                    'intent' => $plan['intent'] ?? null,
                    'tool_name' => $plan['tool_name'] ?? null,
                    'tool_status' => $toolResult['status'] ?? null,
                    'missing_fields' => $toolResult['missing_fields'] ?? [],
                    'date_fields' => array_values(array_intersect(
                        $toolResult['missing_fields'] ?? [],
                        ['check_in_date', 'check_out_date']
                    )),
                ],
            ]);

            $session->update([
                'language' => $plan['language'] ?? $language,
                'last_message_at' => now(),
            ]);

            $log->update([
                'intent' => $plan['intent'] ?? null,
                'language' => $plan['language'] ?? $language,
                'tool_name' => $plan['tool_name'] ?? null,
                'status' => 'completed',
                'plan_payload' => $plan,
                'tool_payload' => $plan['tool_parameters'] ?? [],
                'tool_result' => $toolResult,
                'response_payload' => [
                    'assistant_message' => $assistantText,
                ],
            ]);

            return $assistantMessage;
        } catch (\Throwable $throwable) {
            $fallbackMessage = $language === 'ar'
                ? 'تعذر إكمال الطلب الآن. حاول مرة أخرى بعد قليل أو اطلب المساعدة من الموظف.'
                : 'I could not complete that request right now. Please try again shortly or ask for a human agent.';

            $assistantMessage = $session->messages()->create([
                'role' => 'assistant',
                'message' => $fallbackMessage,
                'metadata' => [
                    'tool_status' => 'failed',
                ],
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $throwable->getMessage(),
                'response_payload' => [
                    'assistant_message' => $fallbackMessage,
                ],
            ]);

            return $assistantMessage;
        }
    }

    private function history(ChatSession $session, ChatMessage $userMessage): Collection
    {
        return $session->messages()
            ->where('id', '<=', $userMessage->id)
            ->latest('id')
            ->take((int) config('chatbot.history_limit', 10))
            ->get()
            ->sortBy('id')
            ->values();
    }

    private function composeAssistantText(string $language, array $plan, array $toolResult, array $knowledge): string
    {
        if (! $this->client->isConfigured()) {
            return $this->fallbackText($language, $toolResult);
        }

        try {
            $response = $this->client->text(
                [[
                    'role' => 'user',
                    'content' => json_encode([
                        'language' => $language,
                        'plan' => $plan,
                        'tool_result' => $toolResult,
                        'knowledge' => $knowledge,
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                ]],
                $this->responseInstructions($language)
            );

            return trim($response['text']) !== ''
                ? trim($response['text'])
                : $this->fallbackText($language, $toolResult);
        } catch (RuntimeException) {
            return $this->fallbackText($language, $toolResult);
        }
    }

    private function responseInstructions(string $language): string
    {
        $languageLabel = $language === 'ar' ? 'Arabic' : 'English';

        return <<<PROMPT
You are the final response writer for a hotel reservation assistant.
Reply in {$languageLabel}.
Use the tool result as the source of truth.
Be warm, concise, and practical.
Always display currency amounts with "SAR" (Saudi Riyal), never use "$" or "USD".
If status is requires_confirmation, clearly ask for a yes/no confirmation.
If status is needs_more_info, ask only for the missing items.
If status is handover, confirm that a human agent was requested.
PROMPT;
    }

    private function fallbackText(string $language, array $toolResult): string
    {
        $status = $toolResult['status'] ?? 'completed';
        $message = $toolResult['message'] ?? '';
        $data = $toolResult['data'] ?? [];
        $missing = $toolResult['missing_fields'] ?? [];

        if ($language === 'ar') {
            return match ($status) {
                'needs_more_info' => 'أحتاج إلى هذه البيانات لإكمال الطلب: '.implode('، ', $missing).'.',
                'requires_confirmation' => $message.' هل تريد مني المتابعة؟',
                'handover' => 'تم وضع هذه المحادثة للتحويل إلى موظف خدمة.',
                'completed' => $this->completedFallbackArabic($data, $message),
                default => $message ?: 'تعذر تنفيذ الطلب حالياً.',
            };
        }

        return match ($status) {
            'needs_more_info' => 'I need these details to continue: '.implode(', ', $missing).'.',
            'requires_confirmation' => ($message ?: 'Your request is ready.').' Do you want me to proceed?',
            'handover' => 'This chat has been flagged for a human agent.',
            'completed' => $this->completedFallbackEnglish($data, $message),
            default => $message ?: 'I could not complete the request right now.',
        };
    }

    private function completedFallbackArabic(array $data, string $message): string
    {
        if (isset($data['reservation_number'])) {
            return "تم إنشاء الحجز بنجاح. رقم الحجز هو {$data['reservation_number']}.";
        }

        if (isset($data['articles'])) {
            return $message ?: 'تم العثور على معلومات من سياسة الفندق.';
        }

        return $message ?: 'تم تنفيذ الطلب بنجاح.';
    }

    private function completedFallbackEnglish(array $data, string $message): string
    {
        if (isset($data['reservation_number'])) {
            return "The reservation was created successfully. Your reservation number is {$data['reservation_number']}.";
        }

        if (isset($data['articles'])) {
            return $message ?: 'I found matching hotel policy information.';
        }

        return $message ?: 'The request was completed successfully.';
    }
}
