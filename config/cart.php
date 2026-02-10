<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Session Cart Feature Flag
    |--------------------------------------------------------------------------
    |
    | This value determines if the session-based cart is enabled.
    | It should be set to false by default and enabled via .env.
    |
    */
    'session_enabled' => env('CART_SESSION_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Session Cart Products Whitelist
    |--------------------------------------------------------------------------
    |
    | A comma-separated list of product IDs that use the session cart
    | features when the feature is enabled.
    |
    */
    'products' => array_filter(array_map('trim', explode(',', env('CART_SESSION_PRODUCTS', '')))),
];
