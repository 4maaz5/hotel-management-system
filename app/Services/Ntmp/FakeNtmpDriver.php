<?php

namespace App\Services\Ntmp;

class FakeNtmpDriver implements NtmpDriver
{
    public function submit(string $eventType, array $payload): array
    {
        return [
            'status' => 'simulated',
            'event_type' => $eventType,
            'reference' => 'NTMP-'.strtoupper($eventType).'-'.now()->format('YmdHis'),
            'message' => 'Saudi NTMP simulation completed successfully.',
            'submitted_at' => now()->toIso8601String(),
            'occupant_count' => count($payload['occupants'] ?? []),
        ];
    }
}
