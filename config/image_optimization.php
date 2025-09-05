<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Resim Optimizasyon Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu dosya resim optimizasyon servisi için konfigürasyon ayarlarını içerir.
    |
    */

    'default_quality' => env('IMAGE_OPTIMIZATION_QUALITY', 85),
    
    'auto_optimize' => env('IMAGE_AUTO_OPTIMIZE', true),
    
    'max_file_size' => env('IMAGE_MAX_FILE_SIZE', 5120), // KB cinsinden
    
    'optimize_threshold' => env('IMAGE_OPTIMIZE_THRESHOLD', 500), // KB cinsinden
    
    'webp_quality' => env('WEBP_QUALITY', 85),
    
    'jpeg_quality' => env('JPEG_QUALITY', 90),
    
    'png_compression' => env('PNG_COMPRESSION', 9),
    
    /*
    |--------------------------------------------------------------------------
    | Resim Boyutları
    |--------------------------------------------------------------------------
    |
    | Farklı kullanım alanları için resim boyutları
    |
    */
    
    'sizes' => [
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
            'quality' => 80,
            'crop' => true,
        ],
        'medium' => [
            'width' => 800,
            'height' => 600,
            'quality' => 85,
            'crop' => false,
        ],
        'large' => [
            'width' => 1920,
            'height' => 1080,
            'quality' => 90,
            'crop' => false,
        ],
        'product_detail' => [
            'width' => 1200,
            'height' => 1200,
            'quality' => 90,
            'crop' => false,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Desteklenen Formatlar
    |--------------------------------------------------------------------------
    |
    | Optimizasyon servisinin desteklediği resim formatları
    |
    */
    
    'supported_formats' => [
        'jpeg',
        'jpg',
        'png',
        'webp',
        'gif',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Çıktı Formatları
    |--------------------------------------------------------------------------
    |
    | Optimizasyon sonrası çıktı formatları
    |
    */
    
    'output_formats' => [
        'webp' => true,
        'jpeg' => true,
        'png' => false, // PNG'yi WebP'ye dönüştür
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Storage Ayarları
    |--------------------------------------------------------------------------
    |
    | Resim dosyalarının saklanacağı disk ve dizin ayarları
    |
    */
    
    'storage' => [
        'disk' => 'public',
        'directory' => 'products',
        'backup_original' => env('IMAGE_BACKUP_ORIGINAL', false),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Performans Ayarları
    |--------------------------------------------------------------------------
    |
    | Optimizasyon performansı için ayarlar
    |
    */
    
    'performance' => [
        'memory_limit' => env('IMAGE_MEMORY_LIMIT', '256M'),
        'time_limit' => env('IMAGE_TIME_LIMIT', 30),
        'batch_size' => env('IMAGE_BATCH_SIZE', 10),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Loglama Ayarları
    |--------------------------------------------------------------------------
    |
    | Optimizasyon işlemleri için loglama ayarları
    |
    */
    
    'logging' => [
        'enabled' => env('IMAGE_LOGGING_ENABLED', true),
        'level' => env('IMAGE_LOG_LEVEL', 'info'),
        'channel' => env('IMAGE_LOG_CHANNEL', 'daily'),
    ],
];
