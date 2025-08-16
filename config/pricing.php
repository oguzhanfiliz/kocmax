<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | B2B/B2C hibrit fiyatlandırma sisteminin konfigürasyonları
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Dealer Discount
    |--------------------------------------------------------------------------
    |
    | Pricing tier'ı olmayan dealer'lar için varsayılan indirim oranı
    |
    */
    'default_dealer_discount' => env('DEFAULT_DEALER_DISCOUNT', 15.0),

    /*
    |--------------------------------------------------------------------------
    | Customer Type Configuration
    |--------------------------------------------------------------------------
    |
    | Müşteri tiplerinin konfigürasyonları
    |
    */
    'customer_types' => [
        'guest' => [
            'label' => 'Liste Fiyatı',
            'discount' => 0.0,
            'tax_included' => true,
        ],
        'B2C' => [
            'label' => 'Perakende Fiyatı',
            'discount' => 0.0,
            'tax_included' => true,
        ],
        'B2B' => [
            'label' => 'Bayi Fiyatı',
            'discount' => null, // Pricing tier'dan alınacak
            'tax_included' => false, // B2B genelde KDV hariç
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing Display Settings
    |--------------------------------------------------------------------------
    |
    | Fiyat gösterim ayarları
    |
    */
    'show_base_price_to_dealers' => env('SHOW_BASE_PRICE_TO_DEALERS', true),
    'show_savings_amount' => env('SHOW_SAVINGS_AMOUNT', true),
    'show_discount_percentage' => env('SHOW_DISCOUNT_PERCENTAGE', true),

    /*
    |--------------------------------------------------------------------------
    | Price Calculation Settings
    |--------------------------------------------------------------------------
    |
    | Fiyat hesaplama ayarları
    |
    */
    'round_prices' => env('ROUND_PRICES', true),
    'price_precision' => env('PRICE_PRECISION', 2),
    'min_discount_to_show' => env('MIN_DISCOUNT_TO_SHOW', 0.01), // %0.01

    /*
    |--------------------------------------------------------------------------
    | Bulk Pricing Settings
    |--------------------------------------------------------------------------
    |
    | Toplu alım indirim ayarları
    |
    */
    'bulk_discount_enabled' => env('BULK_DISCOUNT_ENABLED', true),
    'bulk_discount_tiers' => [
        10 => 2.0,  // 10+ adet %2 indirim
        50 => 5.0,  // 50+ adet %5 indirim
        100 => 8.0, // 100+ adet %8 indirim
        500 => 12.0, // 500+ adet %12 indirim
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    |
    | Vergi ayarları
    |
    */
    'default_tax_rate' => env('DEFAULT_TAX_RATE', 20.0), // KDV %20
    'tax_included_for_b2c' => env('TAX_INCLUDED_FOR_B2C', true),
    'tax_included_for_b2b' => env('TAX_INCLUDED_FOR_B2B', false),
];
