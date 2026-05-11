<?php

namespace App\Services\Ntmp;

interface NtmpDriver
{
    public function submit(string $eventType, array $payload): array;
}
