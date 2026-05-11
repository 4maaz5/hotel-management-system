<?php

namespace App\Services\Chatbot;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class IntentDetectionService
{
    public function __construct(
        private readonly OpenAiResponsesClient $client,
        private readonly LanguageDetector $languageDetector,
    ) {
    }

    public function detect(ChatSession $session, Collection $history, ChatMessage $message, array $knowledge, string $language): array
    {
        if (! $this->client->isConfigured()) {
            return $this->fallbackPlan($session, $message->message, $language);
        }

        $instructions = $this->buildInstructions($session, $knowledge, $language);

        $messages = $history->map(fn (ChatMessage $chatMessage) => [
            'role' => $chatMessage->role,
            'content' => $chatMessage->message,
        ])->all();

        try {
            return $this->client->structured(
                $messages,
                $instructions,
                $this->schema()
            )['parsed'];
        } catch (RuntimeException) {
            return $this->fallbackPlan($session, $message->message, $language);
        }
    }

    private function buildInstructions(ChatSession $session, array $knowledge, string $language): string
    {
        $context = $session->context ?? [];
        $pendingAction = data_get($context, 'pending_action', 'none');
        $pendingSummary = data_get($context, 'pending_summary', '');
        $channel = data_get($context, 'channel', 'dashboard');
        $today = now()->toDateString();

        $knowledgeContext = collect($knowledge)
            ->map(function (array $item, int $index) {
                return ($index + 1).'. '.$item['title'].': '.Str::limit($item['content'], 350);
            })
            ->implode("\n");

        return <<<PROMPT
You are the planning brain for a hotel reservation chatbot inside a Laravel PMS.
Today's date is {$today}.
The response language must be "{$language}" unless the latest user message clearly uses the other supported language.

Return only JSON that matches the provided schema.
Convert relative dates like "tomorrow" into absolute YYYY-MM-DD dates.
Choose one intent from: check_availability, create_booking, cancel_booking, faq.
Choose one tool from: checkAvailability, createBooking, cancelBooking, getHotelPolicies, none.
Never set confirmation=true unless the user clearly confirms the latest pending action.
If required information is missing, keep the intent/tool but leave missing string fields empty.
For FAQ or policy questions, use getHotelPolicies.
If the user requests a human, set handover=true.

Pending action: {$pendingAction}
Pending summary: {$pendingSummary}
Channel: {$channel}

If channel is "website", treat the user as a public website guest.
For website guests, booking confirmation means submitting a booking request through the website flow.
For website guests, do not assume direct cancellation is available in chat.

Relevant knowledge:
{$knowledgeContext}
PROMPT;
    }

    private function schema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['language', 'intent', 'tool_name', 'summary', 'tool_parameters'],
            'properties' => [
                'language' => [
                    'type' => 'string',
                    'enum' => ['en', 'ar'],
                ],
                'intent' => [
                    'type' => 'string',
                    'enum' => ['check_availability', 'create_booking', 'cancel_booking', 'faq'],
                ],
                'tool_name' => [
                    'type' => 'string',
                    'enum' => ['checkAvailability', 'createBooking', 'cancelBooking', 'getHotelPolicies', 'none'],
                ],
                'summary' => [
                    'type' => 'string',
                ],
                'tool_parameters' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => [
                        'check_in_date',
                        'check_out_date',
                        'reservation_type',
                        'room_type',
                        'adults',
                        'children',
                        'guest_name',
                        'phone',
                        'email',
                        'reservation_number',
                        'policy_topic',
                        'confirmation',
                        'handover',
                        'notes',
                    ],
                    'properties' => [
                        'check_in_date' => ['type' => 'string'],
                        'check_out_date' => ['type' => 'string'],
                        'reservation_type' => ['type' => 'string'],
                        'room_type' => ['type' => 'string'],
                        'adults' => ['type' => 'integer'],
                        'children' => ['type' => 'integer'],
                        'guest_name' => ['type' => 'string'],
                        'phone' => ['type' => 'string'],
                        'email' => ['type' => 'string'],
                        'reservation_number' => ['type' => 'string'],
                        'policy_topic' => ['type' => 'string'],
                        'confirmation' => ['type' => 'boolean'],
                        'handover' => ['type' => 'boolean'],
                        'notes' => ['type' => 'string'],
                    ],
                ],
            ],
        ];
    }

    private function fallbackPlan(ChatSession $session, string $message, string $language): array
    {
        $normalized = Str::lower($message);
        $pendingAction = data_get($session->context ?? [], 'pending_action');
        $confirmation = $this->looksLikeConfirmation($normalized);
        $handover = Str::contains($normalized, ['human', 'agent', 'staff', 'موظف', 'بشر', 'موظف خدمة']);

        $intent = 'faq';
        $tool = 'getHotelPolicies';

        if ($pendingAction === 'create_booking' && $confirmation) {
            $intent = 'create_booking';
            $tool = 'createBooking';
        } elseif ($pendingAction === 'cancel_booking' && $confirmation) {
            $intent = 'cancel_booking';
            $tool = 'cancelBooking';
        } elseif (Str::contains($normalized, ['cancel', 'cancellation', 'لغاء', 'إلغاء'])) {
            $intent = 'cancel_booking';
            $tool = 'cancelBooking';
        } elseif (Str::contains($normalized, ['book', 'booking', 'reserve', 'reservation', 'احجز', 'حجز'])) {
            $intent = 'create_booking';
            $tool = 'createBooking';
        } elseif (Str::contains($normalized, ['availability', 'available', 'vacant', 'متاح', 'توفر'])) {
            $intent = 'check_availability';
            $tool = 'checkAvailability';
        } elseif (Str::contains($normalized, ['hello', 'hi', 'مرحبا', 'السلام'])) {
            $tool = 'none';
        }

        return [
            'language' => $this->languageDetector->detect($message, $language),
            'intent' => $intent,
            'tool_name' => $tool,
            'summary' => $message,
            'tool_parameters' => [
                'check_in_date' => '',
                'check_out_date' => '',
                'reservation_type' => '',
                'room_type' => '',
                'adults' => 1,
                'children' => 0,
                'guest_name' => '',
                'phone' => '',
                'email' => '',
                'reservation_number' => '',
                'policy_topic' => $intent === 'faq' ? $message : '',
                'confirmation' => $confirmation,
                'handover' => $handover,
                'notes' => '',
            ],
        ];
    }

    private function looksLikeConfirmation(string $message): bool
    {
        return Str::contains($message, [
            'yes',
            'confirm',
            'go ahead',
            'book it',
            'cancel it',
            'نعم',
            'أكد',
            'اكّد',
            'احجز',
            'الغ',
            'ألغ',
        ]);
    }
}
