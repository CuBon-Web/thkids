<?php

return [
    'pixel_id' => env('FACEBOOK_PIXEL_ID', '2225730044711426'),
    'access_token' => env('FACEBOOK_CAPI_ACCESS_TOKEN'),
    'api_version' => env('FACEBOOK_CAPI_API_VERSION', 'v21.0'),
    'test_event_code' => env('FACEBOOK_CAPI_TEST_EVENT_CODE'),
    'enabled' => env('FACEBOOK_CAPI_ENABLED', true),
];
