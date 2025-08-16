<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class MigrateConfigSettingsSeeder extends Seeder
{
    /**
     * Migrate config settings to database.
     */
    public function run(): void
    {
        $this->migratePricingSettings();
        $this->migrateCampaignSettings();
    }

    /**
     * Migrate pricing.php business logic settings
     */
    private function migratePricingSettings(): void
    {
        $pricingSettings = [
            // 💰 Core Pricing Settings
            [
                'key' => 'pricing.default_dealer_discount',
                'value' => '15.0',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan Bayi İndirim Oranı',
                'description' => 'Pricing tier\'ı olmayan bayiler için varsayılan indirim oranı (%)',
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:100'],
            ],
            
            // 👁️ Display Settings
            [
                'key' => 'pricing.show_base_price_to_dealers',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Bayilere Base Fiyat Göster',
                'description' => 'Bayilere indirimli fiyatın yanında orijinal fiyatı da göster',
            ],
            [
                'key' => 'pricing.show_savings_amount',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Tasarruf Miktarını Göster',
                'description' => 'Ürünlerde ne kadar tasarruf edildiğini göster (₺ olarak)',
            ],
            [
                'key' => 'pricing.show_discount_percentage',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'İndirim Yüzdesini Göster',
                'description' => 'Ürünlerde indirim yüzdesini göster (%15 indirim gibi)',
            ],
            
            // 🔢 Calculation Settings
            [
                'key' => 'pricing.round_prices',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Fiyatları Yuvarla',
                'description' => 'Hesaplanan fiyatları yuvarlak sayılara çevir (99.95 → 100.00)',
            ],
            [
                'key' => 'pricing.price_precision',
                'value' => '2',
                'type' => 'integer',
                'group' => 'pricing',
                'label' => 'Fiyat Hassasiyet',
                'description' => 'Virgülden sonra kaç hane gösterilecek',
                'validation_rules' => ['required', 'integer', 'min:0', 'max:4'],
            ],
            [
                'key' => 'pricing.min_discount_to_show',
                'value' => '0.01',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Minimum İndirim Gösterim Eşiği',
                'description' => 'Bu değerin altındaki indirimler gösterilmez (%)',
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:1'],
            ],
            
            // 📦 Bulk Pricing
            [
                'key' => 'pricing.bulk_discount_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Toplu Alım İndirimi Aktif',
                'description' => 'Çok adet alımlarda otomatik indirim uygulanır',
            ],
            [
                'key' => 'pricing.bulk_discount_tiers',
                'value' => json_encode([
                    '10' => 2.0,
                    '50' => 5.0,
                    '100' => 8.0,
                    '500' => 12.0,
                ]),
                'type' => 'json',
                'group' => 'pricing',
                'label' => 'Toplu Alım İndirim Kademeleri',
                'description' => 'Adet ve indirim oranları (JSON format: {"10": 2.0, "50": 5.0})',
                'validation_rules' => ['required', 'json'],
            ],
            
            // 💸 Tax Settings
            [
                'key' => 'pricing.default_tax_rate',
                'value' => '20.0',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan KDV Oranı',
                'description' => 'Türkiye için varsayılan KDV oranı (%)',
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:50'],
            ],
            [
                'key' => 'pricing.tax_included_for_b2c',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'B2C Fiyatlarda KDV Dahil',
                'description' => 'Bireysel müşterilere KDV dahil fiyat göster',
            ],
            [
                'key' => 'pricing.tax_included_for_b2b',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'B2B Fiyatlarda KDV Dahil',
                'description' => 'Kurumsal müşterilere KDV dahil fiyat göster',
            ],
        ];

        foreach ($pricingSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Migrate campaign.php business logic settings
     */
    private function migrateCampaignSettings(): void
    {
        $campaignSettings = [
            // 🎯 Core Campaign Settings
            [
                'key' => 'campaign.max_stackable_campaigns',
                'value' => '5',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Maksimum Birikmiş Kampanya Sayısı',
                'description' => 'Bir sepette aynı anda kaç kampanya aktif olabilir',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:20'],
            ],
            [
                'key' => 'campaign.default_priority',
                'value' => '0',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Varsayılan Kampanya Önceliği',
                'description' => 'Yeni kampanyalar için varsayılan öncelik değeri',
                'validation_rules' => ['required', 'integer', 'min:-100', 'max:100'],
            ],
            
            // 📊 Campaign Type Limits
            [
                'key' => 'campaign.buy_x_get_y_free.max_free_items',
                'value' => '10',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Al X Öde Y - Max Bedava Ürün',
                'description' => 'Bu kampanya tipinde maksimum bedava ürün sayısı',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:50'],
            ],
            [
                'key' => 'campaign.bundle_discount.max_bundle_count',
                'value' => '5',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Paket İndirimi - Max Paket Sayısı',
                'description' => 'Bir pakette maksimum kaç farklı ürün olabilir',
                'validation_rules' => ['required', 'integer', 'min:2', 'max:20'],
            ],
            [
                'key' => 'campaign.quantity_discount.max_tiers',
                'value' => '10',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Miktar İndirimi - Max Kademe Sayısı',
                'description' => 'Miktar indiriminde maksimum kademe sayısı',
                'validation_rules' => ['required', 'integer', 'min:2', 'max:20'],
            ],
            [
                'key' => 'campaign.tiered_gift.max_gifts',
                'value' => '5',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Kademeli Hediye - Max Hediye Sayısı',
                'description' => 'Bir kampanyada maksimum hediye sayısı',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:10'],
            ],
            [
                'key' => 'campaign.category_combo.max_categories',
                'value' => '5',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Kategori Kombinasyonu - Max Kategori',
                'description' => 'Kategori kombinasyon kampanyasında maksimum kategori sayısı',
                'validation_rules' => ['required', 'integer', 'min:2', 'max:10'],
            ],
            
            // ✅ Validation Limits
            [
                'key' => 'campaign.validation.max_campaign_name_length',
                'value' => '255',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Kampanya Adı - Max Karakter',
                'description' => 'Kampanya adı maksimum karakter sayısı',
                'validation_rules' => ['required', 'integer', 'min:10', 'max:1000'],
            ],
            [
                'key' => 'campaign.validation.max_description_length',
                'value' => '1000',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Kampanya Açıklaması - Max Karakter',
                'description' => 'Kampanya açıklaması maksimum karakter sayısı',
                'validation_rules' => ['required', 'integer', 'min:50', 'max:5000'],
            ],
            [
                'key' => 'campaign.validation.max_usage_limit',
                'value' => '1000000',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Kampanya - Max Kullanım Limiti',
                'description' => 'Bir kampanya maksimum kaç kez kullanılabilir',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:10000000'],
            ],
            [
                'key' => 'campaign.validation.max_usage_limit_per_customer',
                'value' => '1000',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Müşteri Başına - Max Kullanım Limiti',
                'description' => 'Bir müşteri kampanyayı kaç kez kullanabilir',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:10000'],
            ],
            [
                'key' => 'campaign.validation.max_discount_percentage',
                'value' => '100',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Maksimum İndirim Yüzdesi',
                'description' => 'Kampanyalarda uygulanabilecek maksimum indirim oranı (%)',
                'validation_rules' => ['required', 'integer', 'min:1', 'max:100'],
            ],
            [
                'key' => 'campaign.validation.max_discount_amount',
                'value' => '100000',
                'type' => 'float',
                'group' => 'campaign',
                'label' => 'Maksimum İndirim Tutarı',
                'description' => 'Kampanyalarda uygulanabilecek maksimum indirim tutarı (₺)',
                'validation_rules' => ['required', 'numeric', 'min:1', 'max:1000000'],
            ],
        ];

        foreach ($campaignSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
