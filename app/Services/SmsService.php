<?php

namespace App\Services;

use App\Models\Guest;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\SmsUserSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    /**
     * Send SMS to specific user using template key
     */
    public function sendToUser($userId, $templateType, $data = [])
    {
        $user = User::find($userId);

        if (! $user) {
            Log::warning('SMS: User not found');

            return false;
        }

        // Check if this template is enabled for this user
        $template = SmsTemplate::where('type', $templateType)
            ->where('recipient', 'user')
            ->where('enabled', true)
            ->first();

        if (! $template) {
            Log::warning('SMS: Template not found');

            return false;
        }

        $enabled = SmsUserSetting::where('user_id', $userId)
            ->where('sms_template_id', $template->id)
            ->exists();

        if (! $enabled) {
            Log::info("SMS: Template disabled for user {$userId}");

            return false;
        }

        $mobile = $this->normalizePhone(
            data_get($user, 'employment_data.mobile')
                ?? data_get($user, 'contact_info.mobile_number')
                ?? data_get($user, 'contact_info.mobile')
                ?? data_get($user, 'contact_info.phone')
        );

        if (! $mobile) {
            Log::warning("SMS: No mobile found for user {$userId}");

            return false;
        }

        $message = $this->replacePlaceholders($template->message, $data);
        $result = $this->sendToNumber($mobile, $message, [
            'user_id' => $userId,
            'template_type' => $templateType,
            'recipient' => 'user',
        ]);

        return $result['success'];
    }

    /**
     * Replace template placeholders
     */
    private function replacePlaceholders($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }

    /**
     * Send a manual SMS batch to guests and direct numbers.
     */
    public function sendManual(array $recipients, string $message, array $context = []): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $results[] = $this->sendToNumber(
                $recipient['phone'],
                $message,
                array_merge($context, [
                    'source' => $recipient['source'] ?? 'manual',
                    'guest_id' => $recipient['guest_id'] ?? null,
                    'recipient_name' => $recipient['name'] ?? null,
                ])
            );
        }

        return [
            'mode' => $this->deliveryMode(),
            'requested' => count($results),
            'sent' => collect($results)->where('success', true)->count(),
            'failed' => collect($results)->where('success', false)->count(),
            'results' => $results,
        ];
    }

    public function parsePhoneList(?string $input): array
    {
        $valid = [];
        $invalid = [];

        $candidates = preg_split('/[\r\n,;]+/', (string) $input) ?: [];

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);

            if ($candidate === '') {
                continue;
            }

            $normalized = $this->normalizePhone($candidate);

            if ($normalized) {
                $valid[] = $normalized;
            } else {
                $invalid[] = $candidate;
            }
        }

        return [
            'valid' => array_values(array_unique($valid)),
            'invalid' => array_values(array_unique($invalid)),
        ];
    }

    public function normalizePhone(?string $phone): ?string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return null;
        }

        $normalized = preg_replace('/[^\d+]/', '', $phone) ?? '';

        if (str_starts_with($normalized, '00')) {
            $normalized = '+'.substr($normalized, 2);
        }

        if (substr_count($normalized, '+') > 1 || (str_contains($normalized, '+') && ! str_starts_with($normalized, '+'))) {
            return null;
        }

        $digitsOnly = preg_replace('/\D/', '', $normalized) ?? '';

        if (strlen($digitsOnly) < 8 || strlen($digitsOnly) > 15) {
            return null;
        }

        return str_starts_with($normalized, '+') ? '+'.$digitsOnly : $digitsOnly;
    }

    public function isGatewayConfigured(): bool
    {
        return filled(config('services.sms.api_url'));
    }

    public function deliveryMode(): string
    {
        return $this->shouldSimulate() ? 'simulation' : 'gateway';
    }

    public function sendToGuest(int $guestId, string $message, array $context = []): array
    {
        $guest = Guest::find($guestId);

        if (! $guest) {
            return [
                'success' => false,
                'status' => 'failed',
                'phone' => null,
                'error' => 'Guest not found',
            ];
        }

        $phone = $this->normalizePhone($guest->mobile);

        if (! $phone) {
            return [
                'success' => false,
                'status' => 'failed',
                'phone' => null,
                'error' => 'Guest mobile number is invalid',
            ];
        }

        return $this->sendToNumber($phone, $message, array_merge($context, [
            'guest_id' => $guestId,
            'recipient_name' => $guest->full_name,
            'source' => 'guest',
        ]));
    }

    private function sendToNumber(string $mobile, string $message, array $context = []): array
    {
        $mode = $this->deliveryMode();

        if ($this->shouldSimulate()) {
            $result = $this->simulateSend($mobile, $message, $context);
            $this->recordLog($mobile, $message, $mode, $result, $context);

            return $result;
        }

        try {
            $response = Http::timeout((int) config('services.sms.timeout', 15))
                ->acceptJson()
                ->withHeaders(array_filter([
                    'Authorization' => config('services.sms.api_token')
                        ? 'Bearer '.config('services.sms.api_token')
                        : null,
                ]))
                ->post(config('services.sms.api_url'), array_filter([
                    'to' => $mobile,
                    'message' => $message,
                    'sender' => config('services.sms.sender'),
                ], fn ($value) => filled($value)));

            if ($response->successful()) {
                Log::info('SMS SENT', [
                    'to' => $mobile,
                    'context' => $context,
                    'status' => $response->status(),
                ]);

                $result = [
                    'success' => true,
                    'status' => 'sent',
                    'phone' => $mobile,
                    'response_status' => $response->status(),
                    'provider_response' => $response->body(),
                ];

                $this->recordLog($mobile, $message, $mode, $result, $context);

                return $result;
            }

            Log::warning('SMS gateway responded with an error', [
                'to' => $mobile,
                'context' => $context,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $result = [
                'success' => false,
                'status' => 'failed',
                'phone' => $mobile,
                'response_status' => $response->status(),
                'error' => $response->body(),
            ];

            $this->recordLog($mobile, $message, $mode, $result, $context);

            return $result;
        } catch (Throwable $exception) {
            Log::error('SMS gateway request failed', [
                'to' => $mobile,
                'context' => $context,
                'error' => $exception->getMessage(),
            ]);

            $result = [
                'success' => false,
                'status' => 'failed',
                'phone' => $mobile,
                'error' => $exception->getMessage(),
            ];

            $this->recordLog($mobile, $message, $mode, $result, $context);

            return $result;
        }
    }

    /**
     * Simulated sender when no live gateway is configured.
     */
    private function simulateSend(string $mobile, string $message, array $context = []): array
    {
        Log::info('=== SMS SENT (SIMULATION) ===', [
            'to' => $mobile,
            'message' => $message,
            'context' => $context,
        ]);

        return [
            'success' => true,
            'status' => 'simulated',
            'phone' => $mobile,
        ];
    }

    private function shouldSimulate(): bool
    {
        return (bool) config('services.sms.simulate', true) || ! $this->isGatewayConfigured();
    }

    private function recordLog(string $mobile, string $message, string $mode, array $result, array $context = []): void
    {
        SmsLog::create([
            'requested_by' => $context['requested_by'] ?? null,
            'guest_id' => $context['guest_id'] ?? null,
            'phone' => $mobile,
            'recipient_name' => $context['recipient_name'] ?? null,
            'source' => $context['source'] ?? 'manual',
            'sms_type' => $context['sms_type'] ?? null,
            'template_type' => $context['template_type'] ?? null,
            'delivery_mode' => $mode,
            'status' => $result['status'] ?? ($result['success'] ? 'sent' : 'failed'),
            'message_preview' => mb_substr($message, 0, 500),
            'provider_response' => $result['provider_response'] ?? null,
            'error_message' => $result['error'] ?? null,
        ]);
    }
}
