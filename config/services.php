<?php

return [
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'azure_openai' => [
        'endpoint' => env('AZURE_OPENAI_ENDPOINT'),
        'key' => env('AZURE_OPENAI_KEY'),
        'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2024-08-01-preview'),
    ],
];
