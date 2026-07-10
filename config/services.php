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

    // MessageIndia SMS — used for appointment login OTPs and appointment confirmations.
    'messageindia' => [
        'username'    => env('MESSAGEINDIA_USERNAME',    'addagatlaji'),
        'api_key'     => env('MESSAGEINDIA_API_KEY',     'ff5f3185-18fc-43fb-956d-35b239e5a2ed'),
        'sender_name' => env('MESSAGEINDIA_SENDER_NAME', 'SAHMUM'),
        'template_id' => env('MESSAGEINDIA_TEMPLATE_ID', '1707172283887917938'),
        'appointment_template_id' => env('MESSAGEINDIA_APPOINTMENT_TEMPLATE_ID', '1707177744709776264'),
        'pe_id'       => env('MESSAGEINDIA_PE_ID',       '1701172050186152725'),
    ],

];
