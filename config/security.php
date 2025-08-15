<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API Security Settings
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar API güvenlik middleware'i için kullanılır.
    | Development ve production ortamları için farklı değerler ayarlanabilir.
    |
    */

    'api_request_limit' => env('API_REQUEST_LIMIT', app()->environment('local') ? 10000 : 500),

    /*
    |--------------------------------------------------------------------------
    | IP Blacklist Settings
    |--------------------------------------------------------------------------
    |
    | Kalıcı olarak yasaklanacak IP adresleri listesi
    |
    */

    'blacklisted_ips' => [
        // Kalıcı olarak yasaklı IP'ler buraya eklenir
        // '1.2.3.4',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Middleware Settings
    |--------------------------------------------------------------------------
    |
    | Development ortamında güvenlik kontrollerini devre dışı bırakma
    |
    */

    'enabled' => env('API_SECURITY_ENABLED', !app()->environment('local')),
    'strict_mode' => env('API_SECURITY_STRICT_MODE', app()->environment('production')),

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Detection
    |--------------------------------------------------------------------------
    |
    | Şüpheli aktivite tespit ayarları
    |
    */

    'suspicious_activity' => [
        'enabled' => env('SUSPICIOUS_ACTIVITY_DETECTION', !app()->environment('local')),
        'temporary_blacklist_duration' => 3600, // 1 saat
        'request_tracking_duration' => 300, // 5 dakika
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Mode Overrides
    |--------------------------------------------------------------------------
    |
    | Development ortamında daha esnek limitler
    |
    */

    'development_overrides' => [
        'disable_security_checks' => env('DISABLE_API_SECURITY', app()->environment('local')),
        'high_limits' => env('API_HIGH_LIMITS', app()->environment('local')),
        'unlimited_mode' => env('API_UNLIMITED_MODE', false),
    ],
];
