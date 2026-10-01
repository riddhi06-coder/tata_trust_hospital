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

    // WhatsApp — Fortius "Omni" panel (waba.fortius.in.net). Fortius is a
    // transparent proxy in front of Meta's WhatsApp Cloud API, so the send
    // endpoint and JSON payloads follow Meta's Cloud API format exactly:
    //   POST {base_url}/{version}/{phone_number_id}/messages   (Bearer token)
    'whatsapp' => [
        'base_url'        => env('WHATSAPP_BASE_URL', 'https://waba.fortius.in.net'),
        'version'         => env('WHATSAPP_API_VERSION', 'v18.0'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'token'           => env('WHATSAPP_TOKEN'),
        // Shared secret we echo back on the webhook verification handshake.
        'verify_token'    => env('WHATSAPP_VERIFY_TOKEN'),
        // "Book Appointment" sends users to the OTP login page. When unset, the bot
        // falls back to route('frontend.user_login'), which resolves the correct
        // domain + subpath from the incoming request.
        'booking_url'     => env('WHATSAPP_BOOKING_URL'),
        // Image shown in the welcome message. Must be a public JPG/PNG (not webp).
        // When unset, the bot falls back to the site logo PNG via asset().
        'welcome_image'   => env('WHATSAPP_WELCOME_IMAGE'),
    ],

    // MessageIndia SMS — used for appointment login OTPs and appointment confirmations.
    'messageindia' => [
        'username'    => env('MESSAGEINDIA_USERNAME',    'addagatlaji'),
        'api_key'     => env('MESSAGEINDIA_API_KEY',     'ff5f3185-18fc-43fb-956d-35b239e5a2ed'),
        'sender_name' => env('MESSAGEINDIA_SENDER_NAME', 'SAHMUM'),
        'template_id' => env('MESSAGEINDIA_TEMPLATE_ID', '1707172283887917938'),
        'appointment_template_id' => env('MESSAGEINDIA_APPOINTMENT_TEMPLATE_ID', '1707177744709776264'),
        'cancellation_template_id' => env('MESSAGEINDIA_CANCELLATION_TEMPLATE_ID', '1707172283922616212'),
        'reschedule_template_id'   => env('MESSAGEINDIA_RESCHEDULE_TEMPLATE_ID',   '1707172283932407122'),
        'pe_id'       => env('MESSAGEINDIA_PE_ID',       '1701172050186152725'),
    ],

];
