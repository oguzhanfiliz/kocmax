<?php

return [
    'google_merchant' => [
        'enabled' => env('GOOGLE_MERCHANT_FEED_ENABLED', true),
        'target_currency' => env('GOOGLE_MERCHANT_FEED_CURRENCY', 'TRY'),
        'storage_disk' => env('GOOGLE_MERCHANT_FEED_DISK', 'public'),
        'storage_path' => env('GOOGLE_MERCHANT_FEED_PATH', 'feeds/google-merchant.xml'),
        'product_url_base' => env('GOOGLE_MERCHANT_PRODUCT_URL_BASE', env('FRONTEND_URL', env('APP_URL', 'http://127.0.0.1:8000'))),
        'product_url_prefix' => env('GOOGLE_MERCHANT_PRODUCT_URL_PREFIX', 'urun'),
        'mobile_url_base' => env('GOOGLE_MERCHANT_MOBILE_URL_BASE'),
        'asset_url_base' => env('GOOGLE_MERCHANT_ASSET_URL_BASE'),
        'max_additional_images' => env('GOOGLE_MERCHANT_MAX_IMAGES', 10),
        'item_limit' => env('GOOGLE_MERCHANT_ITEM_LIMIT'),
        'default_google_product_category' => env('GOOGLE_MERCHANT_DEFAULT_CATEGORY'),
        'brand' => env('GOOGLE_MERCHANT_BRAND', 'KOCMAX'),
        'category_slug_map' => [
            // 'kategori-slug' => 'Google Taxonomy Id ya da Başlık',
        ],
        'weight_unit' => env('GOOGLE_MERCHANT_WEIGHT_UNIT', 'kg'),
        'dimension_unit' => env('GOOGLE_MERCHANT_DIMENSION_UNIT', 'cm'),
        'channel' => [
            'title' => env('GOOGLE_MERCHANT_CHANNEL_TITLE', env('APP_NAME', 'Application') . ' Catalog'),
            'link' => env('GOOGLE_MERCHANT_CHANNEL_LINK', env('FRONTEND_URL', env('APP_URL', 'http://127.0.0.1:8000'))),
            'description' => env('GOOGLE_MERCHANT_CHANNEL_DESCRIPTION', 'Automated Google Merchant feed'),
            'language' => env('GOOGLE_MERCHANT_CHANNEL_LANGUAGE', env('APP_LOCALE', 'tr')),
        ],
        'schedule' => [
            'enabled' => env('GOOGLE_MERCHANT_SCHEDULE_ENABLED', true),
            'expression' => env('GOOGLE_MERCHANT_SCHEDULE', '0 3 * * *'),
        ],
    ],
];
