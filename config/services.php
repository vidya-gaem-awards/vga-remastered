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

    'abuse_ipdb' => [
        'api_key' => env('ABUSE_IPDB_API_KEY'),
    ],

    'igdb' => [
        'client_id' => env('TWITCH_CLIENT_ID'),
        'client_secret' => env('TWITCH_CLIENT_SECRET'),
    ],

    'cloudflare' => [
        'api_key' => env('CLOUDFLARE_API_KEY'),
        'zone_id' => env('CLOUDFLARE_ZONE_ID'),
    ],

    'steam' => [
        'client_id' => null,
        'client_secret' => env('STEAM_API_KEY'),
        'redirect' => env('STEAM_REDIRECT_URI', '/login/return'),
        'allowed_hosts' => [
            'vidyagaemawards.com',
            'beta.vidyagaemawards.com',
            'vga-nextgen.lndo.site',
            'vga-remastered.lndo.site',
            '2025.vidyagaemawards.com',
        ]
    ],
];
