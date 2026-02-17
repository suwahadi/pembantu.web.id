<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Midtrans Core API
    | Sesuaikan dengan credentials dari Midtrans dashboard
    |
    */

    // Server key dari Midtrans account
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    // Client key dari Midtrans account
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    // Midtrans API base URL
    'api_url' => env('MIDTRANS_API_URL', 'https://app.midtrans.com'),
    'api_sandbox_url' => env('MIDTRANS_API_SANDBOX_URL', 'https://app.sandbox.midtrans.com'),

    // Environment: sandbox atau production
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Callback URL untuk Midtrans notification
    'callback_url' => env('MIDTRANS_CALLBACK_URL', ''),

    // Timeout untuk API request
    'http_client_timeout' => 30,
];
