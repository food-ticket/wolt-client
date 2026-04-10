<?php

return [
    'client_id' => env('WOLT_CLIENT_ID'),

    'client_secret' => env('WOLT_CLIENT_SECRET'),

    'webhook_secret' => env('WOLT_WEBHOOK_SECRET'),

    'redirect_uri' => env('WOLT_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Wolt Environment
    |--------------------------------------------------------------------------
    |
    | Controls which Wolt endpoints are used: "production" or "test".
    | Defaults to "production" when APP_ENV is "production", otherwise "test".
    |
    */
    'environment' => env('WOLT_ENVIRONMENT', env('APP_ENV') === 'production' ? 'production' : 'test'),

    /*
    |--------------------------------------------------------------------------
    | URL Overrides
    |--------------------------------------------------------------------------
    |
    | When set, these override the URLs derived from the environment above.
    |
    */
    'token_url' => env('WOLT_TOKEN_URL'),

    'base_url' => env('WOLT_BASE_URL'),
];
