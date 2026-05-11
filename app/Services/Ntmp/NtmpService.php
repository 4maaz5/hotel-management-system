<?php

namespace App\Services\Ntmp;

use App\Jobs\ProcessNtmpSubmission;
use App\Models\NtmpSetting;
use App\Models\NtmpSubmission;
use App\Models\Reservation;
use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class NtmpService
{
    public function __construct(
        protected NtmpPayloadBuilder $payloadBuilder,
        protected NtmpPayloadValidator $payloadValidator,
    ) {
    }

    public function syncReservationEvent(Reservation $reservation, string $eventType): ?NtmpSubmission
    {
        $setting = NtmpSetting::current();

        if (! $setting->enabled) {
            return null;
        }

        $payload = $this->payloadBuilder->build($reservation, $eventType);
        $payloadHash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $submission = NtmpSubmission::firstOrCreate(
            [
                'reservation_id' => $reservation->id,
                'event_type' => $eventType,
                'payload_hash' => $payloadHash,
            ],
            [
                'guest_id' => $reservation->guest_id,
                'status' => 'queued',
                'request_payload' => $payload,
            ]
        );

        if (! $submission->wasRecentlyCreated) {
            return $submission->fresh();
        }

        try {
            $this->payloadValidator->validate($payload);
        } catch (ValidationException $exception) {
            $submission->update([
                'status' => 'failed',
                'response_payload' => [
                    'validation_errors' => $exception->errors(),
                ],
                'error_message' => 'Saudi NTMP payload validation failed.',
                'attempted_at' => now(),
            ]);

            $setting->forceFill([
                'connection_status' => 'error',
            ])->saveQuietly();

            return $submission->fresh();
        }

        ProcessNtmpSubmission::dispatch($submission->id);

        return $submission->fresh();
    }

    public function processSubmission(NtmpSubmission $submission): NtmpSubmission
    {
        $setting = $this->settingForSubmission($submission);

        if (! $setting || ! $setting->enabled) {
            $submission->update([
                'status' => 'skipped',
                'error_message' => 'Saudi NTMP is disabled for this property.',
                'attempted_at' => now(),
            ]);

            return $submission->fresh();
        }

        $submission->update([
            'status' => 'processing',
            'attempted_at' => now(),
        ]);

        try {
            $response = $this->resolveDriver($setting)->submit(
                $submission->event_type,
                $submission->request_payload ?? []
            );

            $submission->update([
                'status' => $response['status'] ?? 'submitted',
                'response_payload' => $response,
                'external_reference' => $response['reference'] ?? null,
                'error_message' => null,
            ]);

            if (in_array($submission->event_type, ['check_in', 'stay_update'], true)) {
                $submission->reservation?->forceFill([
                    'ntmp_reported_at' => now(),
                ])->saveQuietly();
            }

            $setting->forceFill([
                'connection_status' => $setting->mode === 'live' ? 'connected' : 'simulation_ready',
                'last_synced_at' => now(),
            ])->saveQuietly();

            return $submission->fresh();
        } catch (Throwable $exception) {
            $submission->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            $setting->forceFill([
                'connection_status' => 'error',
            ])->saveQuietly();

            report($exception);

            return $submission->fresh();
        }
    }

    protected function settingForSubmission(NtmpSubmission $submission): ?NtmpSetting
    {
        return NtmpSetting::withoutGlobalScope(TenantScope::class)
            ->withoutGlobalScope(CurrentPropertyScope::class)
            ->where('tenant_id', $submission->tenant_id)
            ->where('property_id', $submission->property_id)
            ->first();
    }

    protected function resolveDriver(NtmpSetting $setting): NtmpDriver
    {
        return match ($setting->driver) {
            'fake' => app(FakeNtmpDriver::class),
            default => throw new InvalidArgumentException("Unsupported Saudi NTMP driver [{$setting->driver}]."),
        };
    }
}
