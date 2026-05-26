<?php

return [

    'azure' => [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'tenant' => env('AZURE_TENANT_ID'),
        'redirect' => env('AZURE_REDIRECT_URI', rtrim((string) env('APP_URL', 'http://localhost'), '/').'/auth/azure/callback'),
        'admin_group_id' => env('AZURE_ADMIN_GROUP_ID'),
        'user_group_id' => env('AZURE_USER_GROUP_ID'),
        'graph_base_url' => env('AZURE_GRAPH_BASE_URL', 'https://graph.microsoft.com/v1.0'),
        'proxy' => env('AZURE_PROXY'),
    ],

    'demo_access' => [
        'enabled' => env('DEMO_ACCESS_ENABLED', false),
        'admin' => env('DEMO_ACCESS_IS_ADMIN', true),
        'name' => env('DEMO_ACCESS_NAME', 'Demo User'),
        'email' => env('DEMO_ACCESS_EMAIL', 'demo@example.test'),
    ],

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

];
