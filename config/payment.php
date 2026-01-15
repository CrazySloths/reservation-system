<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Test Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, reference numbers starting with "TEST-" will be auto-accepted
    | Perfect for development and thesis defense demonstrations
    |
    */
    'test_mode' => env('PAYMENT_TEST_MODE', true),
    
    /*
    |--------------------------------------------------------------------------
    | PayMongo Integration
    |--------------------------------------------------------------------------
    |
    | PayMongo API credentials for automated payment processing
    | Leave empty to disable PayMongo integration
    |
    */
    'paymongo_enabled' => env('PAYMONGO_ENABLED', false),
    'paymongo_secret_key' => env('PAYMONGO_SECRET_KEY', ''),
    'paymongo_public_key' => env('PAYMONGO_PUBLIC_KEY', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Manual Payment Channels
    |--------------------------------------------------------------------------
    |
    | Configuration for manual cashless payment options
    | Citizens will see these options and can enter reference numbers
    |
    */
    'channels' => [
        'gcash' => [
            'enabled' => env('GCASH_ENABLED', true),
            'name' => 'GCash',
            'account_number' => env('GCASH_ACCOUNT_NUMBER', '09171234567'),
            'account_name' => env('GCASH_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Send payment via GCash app, then enter your 13-digit reference number',
            'reference_length' => 13,
            'icon' => 'smartphone',
        ],
        
        'maya' => [
            'enabled' => env('MAYA_ENABLED', true),
            'name' => 'Maya (PayMaya)',
            'account_number' => env('MAYA_ACCOUNT_NUMBER', '09171234567'),
            'account_name' => env('MAYA_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Send payment via Maya app, then enter your transaction reference number',
            'reference_length' => 12,
            'icon' => 'wallet',
        ],
        
        'bpi' => [
            'enabled' => env('BPI_ENABLED', true),
            'name' => 'BPI (Bank of the Philippine Islands)',
            'account_number' => env('BPI_ACCOUNT_NUMBER', 'XXXX-XXXX-XX'),
            'account_name' => env('BPI_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Transfer via BPI online/mobile banking, then enter your transaction reference number',
            'reference_length' => 16,
            'icon' => 'building-2',
        ],
        
        'bdo' => [
            'enabled' => env('BDO_ENABLED', true),
            'name' => 'BDO (Banco de Oro)',
            'account_number' => env('BDO_ACCOUNT_NUMBER', 'XXXX-XXXX-XX'),
            'account_name' => env('BDO_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Transfer via BDO online/mobile banking, then enter your transaction reference number',
            'reference_length' => 15,
            'icon' => 'building-2',
        ],
        
        'metrobank' => [
            'enabled' => env('METROBANK_ENABLED', true),
            'name' => 'Metrobank',
            'account_number' => env('METROBANK_ACCOUNT_NUMBER', 'XXXX-XXXX-XX'),
            'account_name' => env('METROBANK_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Transfer via Metrobank online/mobile banking, then enter your transaction reference number',
            'reference_length' => 14,
            'icon' => 'building-2',
        ],
        
        'unionbank' => [
            'enabled' => env('UNIONBANK_ENABLED', true),
            'name' => 'UnionBank',
            'account_number' => env('UNIONBANK_ACCOUNT_NUMBER', 'XXXX-XXXX-XX'),
            'account_name' => env('UNIONBANK_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Transfer via UnionBank online/mobile banking, then enter your transaction reference number',
            'reference_length' => 12,
            'icon' => 'building-2',
        ],
        
        'landbank' => [
            'enabled' => env('LANDBANK_ENABLED', true),
            'name' => 'Landbank',
            'account_number' => env('LANDBANK_ACCOUNT_NUMBER', 'XXXX-XXXX-XX'),
            'account_name' => env('LANDBANK_ACCOUNT_NAME', 'LGU-1 Treasurer\'s Office'),
            'instructions' => 'Transfer via Landbank online/mobile banking, then enter your transaction reference number',
            'reference_length' => 13,
            'icon' => 'building-2',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Payment Slip Settings
    |--------------------------------------------------------------------------
    */
    'slip_deadline_hours' => env('PAYMENT_DEADLINE_HOURS', 48),
];

