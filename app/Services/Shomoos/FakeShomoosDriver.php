<?php

namespace App\Services\Shomoos;

class FakeShomoosDriver implements ShomoosDriver
{
    public function submit(string $eventType, array $payload): array
    {
        return [
            'status' => 'simulated',
            'event_type' => $eventType,
            'reference' => 'SHM-'.strtoupper($eventType).'-'.now()->format('YmdHis'),
            'message' => 'Shomoos simulation completed successfully.',
            'submitted_at' => now()->toIso8601String(),
            'occupant_count' => count($payload['occupants'] ?? []),
        ];
    }
}
