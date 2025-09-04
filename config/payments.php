<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider that will be used
    | when none is specified explicitly.
    |
    */
    'default' => env('PAYMENT_DEFAULT_PROVIDER', 'paytr'),

    /*
    |--------------------------------------------------------------------------
    | Payment Providers
    |--------------------------------------------------------------------------
    |
    | Here you can configure the supported payment providers and their
    | specific settings. Each provider may require different configuration
    | parameters.
    |
    */
    'providers' => [
        'paytr' => [
            'merchant_id' => env('PAYTR_MERCHANT_ID'),
            'merchant_key' => env('PAYTR_MERCHANT_KEY'),
            'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
            'test_mode' => env('PAYTR_TEST_MODE', true),
            'callback_url' => env('PAYTR_CALLBACK_URL', env('APP_URL') . '/api/webhooks/paytr/callback'),
            'success_url' => env('PAYTR_SUCCESS_URL', env('APP_URL') . '/checkout/success'),
            'failure_url' => env('PAYTR_FAILURE_URL', env('APP_URL') . '/checkout/failed'),
            'timeout_limit' => env('PAYTR_TIMEOUT_LIMIT', 30), // dakika
            'currency' => env('PAYTR_CURRENCY', 'TL'),
            'lang' => env('PAYTR_LANG', 'tr'),
            'max_installment' => env('PAYTR_MAX_INSTALLMENT', 0), // 0 = tek çekim
            'non_3d' => env('PAYTR_NON_3D', 0), // 0 = 3D Secure aktif
        ],

        'iyzico' => [
            'api_key' => env('IYZICO_API_KEY'),
            'secret_key' => env('IYZICO_SECRET_KEY'),
            'base_url' => env('IYZICO_BASE_URL', 'https://api.iyzipay.com'),
            'test_mode' => env('IYZICO_TEST_MODE', true),
        ],

        'stripe' => [
            'public_key' => env('STRIPE_PUBLIC_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'test_mode' => env('STRIPE_TEST_MODE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Transfer Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for bank transfer payment method.
    |
    */
    'bank_transfer' => [
        'account_info' => [
            'bank_name' => env('BANK_TRANSFER_BANK_NAME', 'Örnek Banka A.Ş.'),
            'account_name' => env('BANK_TRANSFER_ACCOUNT_NAME', 'ŞİRKET ADI'),
            'account_number' => env('BANK_TRANSFER_ACCOUNT_NUMBER'),
            'iban' => env('BANK_TRANSFER_IBAN'),
            'swift_code' => env('BANK_TRANSFER_SWIFT_CODE'),
        ],
        'verification_required' => env('BANK_TRANSFER_VERIFICATION_REQUIRED', true),
        'auto_approval_limit' => env('BANK_TRANSFER_AUTO_APPROVAL_LIMIT', 0), // 0 = tüm işlemler manuel
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security related configuration for payment processing.
    |
    */
    'security' => [
        'price_validation_tolerance' => env('PAYMENT_PRICE_TOLERANCE', 0.01), // 1 kuruş tolerance
        'checkout_session_lifetime' => env('PAYMENT_CHECKOUT_SESSION_LIFETIME', 900), // 15 dakika
        'payment_session_lifetime' => env('PAYMENT_SESSION_LIFETIME', 600), // 10 dakika
        'max_refund_days' => env('PAYMENT_MAX_REFUND_DAYS', 30), // gün
        'require_invoice_for_b2b' => env('PAYMENT_REQUIRE_INVOICE_B2B', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for payment endpoints.
    |
    */
    'rate_limiting' => [
        'checkout_attempts_per_minute' => env('PAYMENT_CHECKOUT_RATE_LIMIT', 10),
        'callback_attempts_per_minute' => env('PAYMENT_CALLBACK_RATE_LIMIT', 60),
        'refund_attempts_per_hour' => env('PAYMENT_REFUND_RATE_LIMIT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Payment operation logging settings.
    |
    */
    'logging' => [
        'log_successful_payments' => env('PAYMENT_LOG_SUCCESS', true),
        'log_failed_payments' => env('PAYMENT_LOG_FAILURES', true),
        'log_refunds' => env('PAYMENT_LOG_REFUNDS', true),
        'log_sensitive_data' => env('PAYMENT_LOG_SENSITIVE', false), // ASLA production'da true yapma
        'retention_days' => env('PAYMENT_LOG_RETENTION_DAYS', 90),
    ],
];