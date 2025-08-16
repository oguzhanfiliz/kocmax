<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya sadece teknik ayarları içerir.
    | İş mantığı ayarları artık admin panelinden yönetiliyor.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Customer Type Configuration (Technical)
    |--------------------------------------------------------------------------
    |
    | Müşteri tiplerinin teknik konfigürasyonları
    | NOT: Discount oranları admin panelinden yönetiliyor
    |
    */
    'customer_types' => [
        'guest' => [
            'label' => 'Liste Fiyatı',
            'discount' => 0.0, // Fixed
            'tax_included' => true,
        ],
        'B2C' => [
            'label' => 'Perakende Fiyatı',
            'discount' => 0.0, // Fixed
            'tax_included' => true, // Admin panelden kontrol ediliyor
        ],
        'B2B' => [
            'label' => 'Bayi Fiyatı',
            'discount' => null, // Pricing tier'dan alınacak
            'tax_included' => false, // Admin panelden kontrol ediliyor
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DEPRECATED - Admin Paneline Taşındı
    |--------------------------------------------------------------------------
    |
    | Aşağıdaki ayarlar artık Settings tablosundan okunuyor:
    |
    | - default_dealer_discount → pricing.default_dealer_discount
    | - show_base_price_to_dealers → pricing.show_base_price_to_dealers
    | - show_savings_amount → pricing.show_savings_amount
    | - show_discount_percentage → pricing.show_discount_percentage
    | - round_prices → pricing.round_prices
    | - price_precision → pricing.price_precision
    | - min_discount_to_show → pricing.min_discount_to_show
    | - bulk_discount_enabled → pricing.bulk_discount_enabled
    | - bulk_discount_tiers → pricing.bulk_discount_tiers
    | - default_tax_rate → pricing.default_tax_rate
    | - tax_included_for_b2c → pricing.tax_included_for_b2c
    | - tax_included_for_b2b → pricing.tax_included_for_b2b
    |
    | Kullanım: SettingHelper::get('pricing.default_dealer_discount', 15.0)
    |
    */
];
