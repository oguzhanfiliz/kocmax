<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') ? 
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS'))) : [
            'http://localhost:3000', 
            'http://localhost:5173', 
            'http://127.0.0.1:3000',
            'https://kocmax.mutfakyapim.net',
            'https://b2bb2c.mutfakyapim.net',
            'https://kocmax.netlify.app',
            'https://b2bb2c-frontend.vercel.app'
        ],

    'allowed_origins_patterns' => [
        // Allow subdomains in production
        '/^https:\/\/.*\.yourdomain\.com$/',
    ],

    'allowed_headers' => [
        'Accept',
        'Authorization', 
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-Socket-ID',
        'Origin',
        'User-Agent',
        'Cache-Control'
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset'
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true, // Required for Sanctum

    /*
    |--------------------------------------------------------------------------
    | Domain-based API Protection
    |--------------------------------------------------------------------------
    |
    | Domain koruması için izin verilen domain listesi
    | Wildcard subdomain desteklenir: *.example.com
    |
    */

    'allowed_domains' => array_filter(
        array_map('trim', explode(',', env('ALLOWED_DOMAINS', 'localhost:3000,127.0.0.1:3000,localhost:5173')))
    ),

    /*
    |--------------------------------------------------------------------------
    | Production Domains
    |--------------------------------------------------------------------------
    |
    | Production'da kullanılacak domain listesi
    |
    */

    // Production domains için .env'den oku, yoksa default değerler
    'production_domains' => env('PRODUCTION_DOMAINS') ? 
        array_map('trim', explode(',', env('PRODUCTION_DOMAINS'))) : [
            'b2bb2c.mutfakyapim.net',
            'www.b2bb2c.mutfakyapim.net',
            'kocmax.mutfakyapim.net',
            'kocmax.netlify.app',
            'b2bb2c-frontend.vercel.app',
            '*.mutfakyapim.net',
        ],

];
