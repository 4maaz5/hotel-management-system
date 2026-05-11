<?php

namespace App\Services\Shomoos;

interface ShomoosDriver
{
    public function submit(string $eventType, array $payload): array;
}
