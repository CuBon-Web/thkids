<?php

return [
    'enabled' => env('KIOTVIET_ENABLED', false),
    'client_id' => env('KIOTVIET_CLIENT_ID'),
    'client_secret' => env('KIOTVIET_CLIENT_SECRET'),
    'retailer' => env('KIOTVIET_RETAILER'),
    'branch_id' => env('KIOTVIET_BRANCH_ID'),
    'token_url' => env('KIOTVIET_TOKEN_URL', 'https://id.kiotviet.vn/connect/token'),
    'api_base' => env('KIOTVIET_API_BASE', 'https://public.kiotapi.com'),
    'token_cache_key' => 'kiotviet_access_token',
    'token_ttl' => 23 * 60 * 60,
];
