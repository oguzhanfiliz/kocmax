<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Campaign System Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya sadece teknik ayarları içerir.
    | İş mantığı ayarları artık admin panelinden yönetiliyor.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Technical Settings (Cache, Performance)
    |--------------------------------------------------------------------------
    |
    | Sistemin performansını etkileyen teknik ayarlar
    |
    */
    'caching_enabled' => env('CAMPAIGN_CACHING_ENABLED', true),
    
    'cache_lifetime' => env('CAMPAIGN_CACHE_LIFETIME', 3600), // seconds
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration (Technical)
    |--------------------------------------------------------------------------
    |
    | Kampanya sisteminin loglama ayarları
    |
    */
    'logging' => [
        'enabled' => env('CAMPAIGN_LOGGING_ENABLED', true),
        'channel' => env('CAMPAIGN_LOG_CHANNEL', 'single'),
        'level' => env('CAMPAIGN_LOG_LEVEL', 'info'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | DEPRECATED - Admin Paneline Taşındı
    |--------------------------------------------------------------------------
    |
    | Aşağıdaki ayarlar artık Settings tablosundan okunuyor:
    |
    | - default_priority → campaign.default_priority
    | - max_stackable_campaigns → campaign.max_stackable_campaigns
    | - types.buy_x_get_y_free.max_free_items → campaign.buy_x_get_y_free.max_free_items
    | - types.bundle_discount.max_bundle_count → campaign.bundle_discount.max_bundle_count
    | - types.quantity_discount.max_tiers → campaign.quantity_discount.max_tiers
    | - types.tiered_gift.max_gifts → campaign.tiered_gift.max_gifts
    | - types.category_combo.max_categories → campaign.category_combo.max_categories
    | - validation.max_campaign_name_length → campaign.validation.max_campaign_name_length
    | - validation.max_description_length → campaign.validation.max_description_length
    | - validation.max_usage_limit → campaign.validation.max_usage_limit
    | - validation.max_usage_limit_per_customer → campaign.validation.max_usage_limit_per_customer
    | - validation.max_discount_percentage → campaign.validation.max_discount_percentage
    | - validation.max_discount_amount → campaign.validation.max_discount_amount
    |
    | Kullanım: SettingHelper::get('campaign.max_stackable_campaigns', 5)
    |
    */
];