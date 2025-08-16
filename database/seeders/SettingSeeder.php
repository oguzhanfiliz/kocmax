<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // 💰 Pricing Settings
            [
                'key' => 'pricing.default_dealer_discount',
                'value' => '15.0',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan Bayi İndirim Oranı',
                'description' => 'Pricing tier\'ı olmayan bayiler için varsayılan indirim oranı (%)',
                'is_public' => false,
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:100'],
            ],
            [
                'key' => 'pricing.show_base_price_to_dealers',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Bayilere Liste Fiyatı Göster',
                'description' => 'Bayilere indirimli fiyatın yanında liste fiyatını da gösterilsin mi?',
                'is_public' => false,
            ],
            [
                'key' => 'pricing.show_savings_amount',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Tasarruf Tutarını Göster',
                'description' => 'Müşterilere ne kadar tasarruf ettikleri gösterilsin mi?',
                'is_public' => true,
            ],
            [
                'key' => 'pricing.default_tax_rate',
                'value' => '20.0',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan KDV Oranı',
                'description' => 'Varsayılan KDV oranı (%)',
                'is_public' => true,
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:100'],
            ],
            [
                'key' => 'pricing.bulk_discount_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'pricing',
                'label' => 'Toplu Alım İndirimi',
                'description' => 'Toplu alım indirimleri aktif olsun mu?',
                'is_public' => false,
            ],
            [
                'key' => 'pricing.bulk_discount_tiers',
                'value' => '{"10": 2.0, "50": 5.0, "100": 8.0, "500": 12.0}',
                'type' => 'json',
                'group' => 'pricing',
                'label' => 'Toplu Alım İndirim Kademeleri',
                'description' => 'Adet bazlı indirim oranları (adet: indirim%)',
                'is_public' => false,
                'validation_rules' => ['required', 'json'],
            ],

            // 🎯 Campaign Settings
            [
                'key' => 'campaign.max_stackable_campaigns',
                'value' => '5',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Maksimum Birleştirilebilir Kampanya',
                'description' => 'Bir sepette aynı anda uygulanabilecek maksimum kampanya sayısı',
                'is_public' => false,
                'validation_rules' => ['required', 'integer', 'min:1', 'max:20'],
            ],
            [
                'key' => 'campaign.default_priority',
                'value' => '0',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Varsayılan Kampanya Önceliği',
                'description' => 'Yeni kampanyalar için varsayılan öncelik değeri',
                'is_public' => false,
                'validation_rules' => ['required', 'integer', 'min:0'],
            ],
            [
                'key' => 'campaign.max_usage_limit',
                'value' => '1000000',
                'type' => 'integer',
                'group' => 'campaign',
                'label' => 'Maksimum Kampanya Kullanım Limiti',
                'description' => 'Bir kampanya için maksimum kullanım limiti',
                'is_public' => false,
                'validation_rules' => ['required', 'integer', 'min:1'],
            ],
            [
                'key' => 'campaign.max_discount_percentage',
                'value' => '100',
                'type' => 'float',
                'group' => 'campaign',
                'label' => 'Maksimum İndirim Yüzdesi',
                'description' => 'Kampanyalarda uygulanabilecek maksimum indirim oranı (%)',
                'is_public' => false,
                'validation_rules' => ['required', 'numeric', 'min:0', 'max:100'],
            ],

            // ⚙️ System Settings
            [
                'key' => 'system.site_name',
                'value' => 'İş Sağlığı Güvenliği Kıyafetleri E-Ticaret',
                'type' => 'string',
                'group' => 'system',
                'label' => 'Site Adı',
                'description' => 'Web sitesinin genel adı',
                'is_public' => true,
                'validation_rules' => ['required', 'string', 'max:255'],
            ],
            [
                'key' => 'system.maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'Bakım Modu',
                'description' => 'Site bakım modunda mı?',
                'is_public' => false,
            ],
            [
                'key' => 'system.cache_lifetime',
                'value' => '3600',
                'type' => 'integer',
                'group' => 'system',
                'label' => 'Cache Yaşam Süresi (saniye)',
                'description' => 'Genel cache yaşam süresi',
                'is_public' => false,
                'validation_rules' => ['required', 'integer', 'min:300'],
            ],

            // 🚚 Shipping Settings
            [
                'key' => 'shipping.free_shipping_threshold',
                'value' => '500.00',
                'type' => 'float',
                'group' => 'shipping',
                'label' => 'Ücretsiz Kargo Minimum Tutar',
                'description' => 'Ücretsiz kargo için minimum sepet tutarı (₺)',
                'is_public' => true,
                'validation_rules' => ['required', 'numeric', 'min:0'],
            ],
            [
                'key' => 'shipping.standard_shipping_cost',
                'value' => '25.00',
                'type' => 'float',
                'group' => 'shipping',
                'label' => 'Standart Kargo Ücreti',
                'description' => 'Varsayılan kargo ücreti (₺)',
                'is_public' => true,
                'validation_rules' => ['required', 'numeric', 'min:0'],
            ],

            // 🔔 Notification Settings
            [
                'key' => 'notification.email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'E-posta Bildirimleri',
                'description' => 'E-posta bildirimleri gönderilsin mi?',
                'is_public' => false,
            ],
            [
                'key' => 'notification.admin_email',
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'notification',
                'label' => 'Admin E-posta Adresi',
                'description' => 'Sistem bildirimlerinin gönderileceği admin e-posta adresi',
                'is_public' => false,
                'validation_rules' => ['required', 'email'],
            ],

            // 🔗 API Settings
            [
                'key' => 'api.rate_limit_per_minute',
                'value' => '60',
                'type' => 'integer',
                'group' => 'api',
                'label' => 'API Dakika Başına İstek Limiti',
                'description' => 'API için dakika başına maksimum istek sayısı',
                'is_public' => false,
                'validation_rules' => ['required', 'integer', 'min:10', 'max:1000'],
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::firstOrCreate(
                ['key' => $settingData['key']],
                array_merge($settingData, [
                    'created_by' => 1, // Admin user
                    'updated_by' => 1,
                ])
            );
        }

        $this->command->info('✅ Settings seeded successfully!');
    }
}