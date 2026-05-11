<?php

namespace App\Services\Chatbot;

class LanguageDetector
{
    public function detect(string $message, ?string $fallback = null): string
    {
        if (preg_match('/\p{Arabic}/u', $message) === 1) {
            return 'ar';
        }

        return $fallback === 'ar' ? 'ar' : 'en';
    }
}
