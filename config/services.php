<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'simulate' => env('SMS_SIMULATE', true),
        'api_url' => env('SMS_API_URL'),
        'api_token' => env('SMS_API_TOKEN'),
        'sender' => env('SMS_SENDER'),
        'timeout' => env('SMS_TIMEOUT', 15),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
        'timeout' => env('OPENAI_TIMEOUT', 30),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    'shomoos' => [
        'default_mode' => env('SHOMOOS_DEFAULT_MODE', 'simulation'),
        'default_driver' => env('SHOMOOS_DEFAULT_DRIVER', 'fake'),
    ],

    'ntmp' => [
        'default_mode' => env('NTMP_DEFAULT_MODE', 'simulation'),
        'default_driver' => env('NTMP_DEFAULT_DRIVER', 'fake'),
    ],

];
