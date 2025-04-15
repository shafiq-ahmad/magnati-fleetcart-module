<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Magnati Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can specify the configuration for the Magnati payment gateway.
    |
    */

    // Test mode toggle
    'test_mode' => env('MAGNATI_TEST_MODE', true),
    
    // API URLs
    'test_api_url' => env('MAGNATI_TEST_API_URL', 'https://demo-ipg.ctdev.comtrust.ae:2443'),
    'api_url' => env('MAGNATI_API_URL', 'https://ipg.comtrust.ae:2443'),
    
    // Test credentials
    'test' => [
        'username' => env('MAGNATI_TEST_USERNAME', 'Demo_fY9c'),
        'password' => env('MAGNATI_TEST_PASSWORD', 'Comtrust@20182018'),
        'customer' => env('MAGNATI_TEST_CUSTOMER', 'Demo Merchant'),
        'store' => env('MAGNATI_TEST_STORE', '0000'),
        'terminal' => env('MAGNATI_TEST_TERMINAL', '0000'),
        'transaction_hint' => env('MAGNATI_TEST_TRANSACTION_HINT', 'CPT:Y;VCC:Y'),
    ],
    
    // Production credentials
    'production' => [
        'username' => env('MAGNATI_USERNAME', ''),
        'password' => env('MAGNATI_PASSWORD', ''),
        'customer' => env('MAGNATI_CUSTOMER', ''),
        'store' => env('MAGNATI_STORE', ''),
        'terminal' => env('MAGNATI_TERMINAL', ''),
        'transaction_hint' => env('MAGNATI_TRANSACTION_HINT', 'CPT:Y;VCC:Y'),
    ],
    
    // Test card details for reference
    'test_cards' => [
        'visa_success' => '4111111111111111',
        'visa_insufficient_funds' => '4012888888881881',
        'visa_do_not_honor' => '5105105105105100',
        'mastercard_success' => '5555555555554444',
    ],
];
