<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Store credentials for third-party services (mail, OAuth, notifications).
    | Keep secrets in your .env; these values are safe to be cached.
    |
    */

    // Email providers
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'     => env('AWS_ACCESS_KEY_ID'),
        'secret'  => env('AWS_SECRET_ACCESS_KEY'),
        'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Slack notifications
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // --- OAuth providers ----------------------------------------------------

    // Google (Laravel Socialite)
    // Make sure to set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET,
    // and GOOGLE_REDIRECT_URI in your .env. Example redirect:
    //   https://your-app.test/auth/google/callback
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
    ],

];
