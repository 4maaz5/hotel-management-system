<?php

return [
    'history_limit' => env('CHATBOT_HISTORY_LIMIT', 10),
    'availability_cache_ttl' => env('CHATBOT_AVAILABILITY_CACHE_TTL', 120),
    'poll_interval_ms' => env('CHATBOT_POLL_INTERVAL_MS', 1800),
    'max_messages_per_minute' => env('CHATBOT_RATE_LIMIT_PER_MINUTE', 20),
];
