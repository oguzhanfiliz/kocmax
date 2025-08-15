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

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS') ? 
        explode(',', env('CORS_ALLOWED_ORIGINS')) : 
        ['http://localhost:3000', 'http://localhost:5173', 'http://127.0.0.1:3000'],

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
        explode(',', env('ALLOWED_DOMAINS', 'localhost:3000,127.0.0.1:3000,localhost:8080'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Production Domains
    |--------------------------------------------------------------------------
    |
    | Production'da kullanılacak domain listesi
    |
    */

    'production_domains' => [
        'yourdomain.com',
        'www.yourdomain.com',
        '*.yourdomain.com', // Subdomains
    ],

];
