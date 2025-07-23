<?php

declare(strict_types=1);

namespace App\Enums\Campaign;

enum CampaignType: string
{
    case BUY_X_GET_Y_FREE = 'buy_x_get_y_free'; // "X ürün al Y hediye"
    case BUNDLE_DISCOUNT = 'bundle_discount'; // "Bu ürünleri birlikte al %X indirim"
    case FREE_SHIPPING = 'free_shipping'; // "Ücretsiz kargo"
    case FLASH_SALE = 'flash_sale'; // "Flaş indirim"

    public function getLabel(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => 'X Al Y Hediye',
            self::BUNDLE_DISCOUNT => 'Paket İndirim',
            self::FREE_SHIPPING => 'Ücretsiz Kargo',
            self::FLASH_SALE => 'Flaş İndirim',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => 'Belirtilen ürün/ürünleri alana hediye ürün. Esnek kombinasyonlar yapılabilir.',
            self::BUNDLE_DISCOUNT => 'Belirli ürünleri birlikte alanlara indirim',
            self::FREE_SHIPPING => 'Kargo ücreti muafiyeti',
            self::FLASH_SALE => 'Sınırlı süre indirim kampanyası',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => '🎁',
            self::BUNDLE_DISCOUNT => '📦',
            self::FREE_SHIPPING => '🚚',
            self::FLASH_SALE => '⚡',
        };
    }

    public function requiresProducts(): bool
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE,
            self::BUNDLE_DISCOUNT => true,
            self::FREE_SHIPPING,
            self::FLASH_SALE => false,
        };
    }

    public function getDetailedDescription(): string
    {
        return match($this) {
            self::BUY_X_GET_Y_FREE => 
                "**Esnek Hediye Kampanyası**\n\n" .
                "• **Basit örnek**: \"3 Kask al, 1 Eldiven hediye\"\n" .
                "• **Karmaşık örnek**: \"Kask + Eldiven + Bot al, Gözlük hediye\"\n" .
                "• **Çoklu örnek**: \"Herhangi 5 ürün al, istediğin 1 ürün hediye\"\n\n" .
                "**Kurallar**:\n" .
                "- Tetikleyici ürünler tanımlanır\n" .
                "- Hediye ürünler seçilir\n" .
                "- Minimum adet şartları konur\n" .
                "- \"Tümü gerekli\" veya \"Herhangi biri\" seçenekleri",
            
            self::BUNDLE_DISCOUNT => 
                "**Paket İndirim Kampanyası**\n\n" .
                "• **Örnek**: \"Kask + Eldiven + Bot = %20 indirim\"\n" .
                "• **Sabit fiyat**: \"Bu 3 ürün sadece 500₺\"\n\n" .
                "**Kurallar**:\n" .
                "- Paket ürünleri belirlenir\n" .
                "- İndirim tipi: Yüzde, sabit tutar, sabit fiyat\n" .
                "- En ucuz ürün bedava seçeneği\n" .
                "- Maksimum indirim limiti",
            
            self::FREE_SHIPPING => 
                "**Ücretsiz Kargo Kampanyası**\n\n" .
                "• **200₺ üzeri kargo bedava**\n" .
                "• **Belirli ürünlerde kargo bedava**\n\n" .
                "**Koşullar**:\n" .
                "- Minimum sepet tutarı\n" .
                "- Belirli ürün/kategori\n" .
                "- Müşteri türü\n" .
                "- Coğrafi bölge",
            
            self::FLASH_SALE => 
                "**Flaş İndirim Kampanyası**\n\n" .
                "• **24 saat %50 indirim**\n" .
                "• **Stok tükene kadar %30 indirim**\n\n" .
                "**Özellikler**:\n" .
                "- Sınırlı süre\n" .
                "- Aciliyet hissi\n" .
                "- Stok bazlı limit\n" .
                "- Yüksek indirim oranları"
        };
    }
}