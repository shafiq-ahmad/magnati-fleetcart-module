<?php

return [
    'tabs' => [
        'group' => [
            'magnati_settings' => 'Magnati Settings',
        ],
        'general' => 'General',
        'credentials' => 'API Credentials',
    ],
    'settings' => [
        'enabled' => 'Enabled',
        'label' => 'Label',
        'description' => 'Description',
        'test_mode' => 'Test Mode',
        
        // Test credentials
        'test_username' => 'Test Username',
        'test_password' => 'Test Password',
        'test_customer' => 'Test Customer',
        'test_store' => 'Test Store',
        'test_terminal' => 'Test Terminal',
        'test_transaction_hint' => 'Test Transaction Hint',
        
        // Production credentials
        'username' => 'Production Username',
        'password' => 'Production Password',
        'customer' => 'Production Customer',
        'store' => 'Production Store',
        'terminal' => 'Production Terminal',
        'transaction_hint' => 'Production Transaction Hint',
    ],
    'messages' => [
        'payment_success' => 'Payment completed successfully!',
        'payment_failed' => 'Payment failed. Please try again.',
        'payment_canceled' => 'Payment was canceled.',
    ],
];
