<?php

namespace App\Services\Shomoos;

use App\Jobs\ProcessShomoosSubmission;
use App\Models\Reservation;
use App\Models\Scopes\CurrentPropertyScope;
use App\Models\Scopes\TenantScope;
use App\Models\ShomoosSetting;
use App\Models\ShomoosSubmission;
use InvalidArgumentException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ShomoosService
{
    public function __construct(
        protected ShomoosPayloadBuilder $payloadBuilder,
        protected ShomoosPayloadValidator $payloadValidator,
    ) {
    }

    public function syncReservationEvent(Reservation $reservation, string $eventType): ?ShomoosSubmission
    {
        $setting = ShomoosSetting::current();

        if (! $setting->enabled) {
            return null;
        }

        $payload = $this->payloadBuilder->build($reservation, $eventType);

        $submission = ShomoosSubmission::create([
            'company_id' => $reservation->company_id,
            'branch_id' => $reservation->branch_id,
            'reservation_id' => $reservation->id,
            'guest_id' => $reservation->guest_id,
            'event_type' => $eventType,
            'status' => 'queued',
            'request_payload' => $payload,
        ]);

        try {
            $this->payloadValidator->validate($payload);
        } catch (ValidationException $exception) {
            $submission->update([
                'status' => 'failed',
                'response_payload' => [
                    'validation_errors' => $exception->errors(),
                ],
                'error_message' => 'Shomoos payload validation failed.',
                'attempted_at' => now(),
            ]);

            $setting->forceFill([
                'connection_status' => 'error',
            ])->saveQuietly();

            return $submission->fresh();
        }

        ProcessShomoosSubmission::dispatch($submission->id);

        return $submission->fresh();
    }

    public function processSubmission(ShomoosSubmission $submission): ShomoosSubmission
    {
        $setting = $this->settingForSubmission($submission);

        if (! $setting || ! $setting->enabled) {
            $submission->update([
                'status' => 'skipped',
                'error_message' => 'Shomoos is disabled for this property.',
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
                    'shomoos_reported_at' => now(),
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

    protected function settingForSubmission(ShomoosSubmission $submission): ?ShomoosSetting
    {
        return ShomoosSetting::withoutGlobalScope(TenantScope::class)
            ->withoutGlobalScope(CurrentPropertyScope::class)
            ->where('company_id', $submission->company_id)
            ->where('branch_id', $submission->branch_id)
            ->first();
    }

    protected function resolveDriver(ShomoosSetting $setting): ShomoosDriver
    {
        return match ($setting->driver) {
            'fake' => app(FakeShomoosDriver::class),
            default => throw new InvalidArgumentException("Unsupported Shomoos driver [{$setting->driver}]."),
        };
    }
}
