<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Campaign System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the campaign system.
    |
    */

    'caching_enabled' => env('CAMPAIGN_CACHING_ENABLED', true),
    
    'cache_lifetime' => env('CAMPAIGN_CACHE_LIFETIME', 3600), // seconds
    
    'default_priority' => env('CAMPAIGN_DEFAULT_PRIORITY', 0),
    
    'max_stackable_campaigns' => env('CAMPAIGN_MAX_STACKABLE', 5),
    
    /*
    |--------------------------------------------------------------------------
    | Campaign Types Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for different campaign types and their default settings.
    |
    */
    
    'types' => [
        'buy_x_get_y_free' => [
            'max_free_items' => 10,
            'default_stackable' => false,
        ],
        
        'bundle_discount' => [
            'max_bundle_count' => 5,
            'default_stackable' => true,
        ],
        
        'quantity_discount' => [
            'max_tiers' => 10,
            'default_stackable' => false,
        ],
        
        'tiered_gift' => [
            'max_gifts' => 5,
            'default_stackable' => true,
        ],
        
        'category_combo' => [
            'max_categories' => 5,
            'default_stackable' => true,
        ],
        
        'free_shipping' => [
            'default_stackable' => false,
        ],
        
        'flash_sale' => [
            'default_stackable' => false,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Default validation rules for campaign JSON schemas.
    |
    */
    
    'validation' => [
        'max_campaign_name_length' => 255,
        'max_description_length' => 1000,
        'max_usage_limit' => 1000000,
        'max_usage_limit_per_customer' => 1000,
        'min_cart_amount' => 0,
        'max_cart_amount' => 1000000,
        'max_discount_percentage' => 100,
        'max_discount_amount' => 100000,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging for campaign system.
    |
    */
    
    'logging' => [
        'enabled' => env('CAMPAIGN_LOGGING_ENABLED', true),
        'channel' => env('CAMPAIGN_LOG_CHANNEL', 'single'),
        'level' => env('CAMPAIGN_LOG_LEVEL', 'info'),
    ],
];