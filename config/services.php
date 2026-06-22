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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'auth_api' => [
        'url' => rtrim((string) env('AUTH_API_URL', 'http://127.0.0.1:8000'), '/'),
        'system_key' => (string) env('AUTH_API_SYSTEM_KEY', ''),
        'profile_path' => env('AUTH_API_PROFILE_PATH', '/api/auth/profile'),
        'password_path' => env('AUTH_API_PASSWORD_PATH', '/api/integrations/users/{id}/password'),
        'verify' => filter_var(env('AUTH_API_VERIFY', true), FILTER_VALIDATE_BOOL),
        'ca_bundle' => env('AUTH_API_CA_BUNDLE'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
        'timeout' => (int) env('OPENAI_TIMEOUT', 20),
    ],

];
