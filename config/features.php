<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Bu dosya projede kullanılan feature flag'leri içerir.
    | Production'da güvenli deploy için kullanılır.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Smart Pricing System
    |--------------------------------------------------------------------------
    |
    | B2B/B2C hybrid fiyatlandırma sistemini kontrol eder.
    | true: Product listelerinde kullanıcıya özel fiyatlar gösterilir
    | false: Sadece base price gösterilir (legacy mode)
    |
    */
    'smart_pricing_enabled' => env('FEATURE_SMART_PRICING', true),
    
    /*
    |--------------------------------------------------------------------------
    | Smart Pricing Cache
    |--------------------------------------------------------------------------
    |
    | Smart pricing için cache ayarları
    |
    */
    'smart_pricing_cache_ttl' => env('SMART_PRICING_CACHE_TTL', 300), // 5 dakika
    'smart_pricing_cache_enabled' => env('SMART_PRICING_CACHE_ENABLED', true),
    
    /*
    |--------------------------------------------------------------------------
    | Performance Features
    |--------------------------------------------------------------------------
    |
    | Performans optimizasyonları için feature flag'ler
    |
    */
    'lazy_load_variants' => env('FEATURE_LAZY_LOAD_VARIANTS', true),
    'cache_product_filters' => env('FEATURE_CACHE_PRODUCT_FILTERS', true),
    'enable_product_search_cache' => env('FEATURE_PRODUCT_SEARCH_CACHE', true),
    
    /*
    |--------------------------------------------------------------------------
    | API Features
    |--------------------------------------------------------------------------
    |
    | API özelliklerini kontrol eden flag'ler
    |
    */
    'api_rate_limiting_enabled' => env('FEATURE_API_RATE_LIMITING', false), // Development için devre dışı
    'api_domain_protection_enabled' => env('FEATURE_API_DOMAIN_PROTECTION', true),
    'api_security_middleware_enabled' => env('FEATURE_API_SECURITY_MIDDLEWARE', true),
];
