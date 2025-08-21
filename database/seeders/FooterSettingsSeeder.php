<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class FooterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $footerSettings = [
            [
                'key' => 'footer_widget_title',
                'value' => 'Bizimle İletişime Geçin',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Footer Widget Başlığı',
                'description' => 'Footer sol tarafındaki widget başlığı',
                'is_public' => true,
            ],
            [
                'key' => 'footer_description',
                'value' => 'Yüksek kaliteli iş güvenliği ürünleri ve ekipmanları ile güvenli çalışma ortamları oluşturuyoruz. 25 yıllık deneyimimizle sizlere en iyi hizmeti sunuyoruz.',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Footer Açıklama',
                'description' => 'Footer bölümündeki açıklama metni',
                'is_public' => true,
            ],
            [
                'key' => 'footer_account_title',
                'value' => 'Hesabım',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Hesap Menü Başlığı',
                'description' => 'Footer hesap menüsü başlığı',
                'is_public' => true,
            ],
            [
                'key' => 'footer_info_title',
                'value' => 'Bilgiler',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Bilgiler Menü Başlığı',
                'description' => 'Footer bilgiler menüsü başlığı',
                'is_public' => true,
            ],
            [
                'key' => 'footer_call_text',
                'value' => 'Sorunuz mu var? Bizi arayın',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Footer Çağrı Metni',
                'description' => 'Footer bölümündeki çağrı metni',
                'is_public' => true,
            ],
            [
                'key' => 'footer_account_items',
                'value' => json_encode([
                    ['title' => 'Hesabım', 'url' => '/account'],
                    ['title' => 'Siparişlerim', 'url' => '/orders'],
                    ['title' => 'Favorilerim', 'url' => '/favorites'],
                    ['title' => 'Adres Defterim', 'url' => '/addresses'],
                    ['title' => 'Kargo Takip', 'url' => '/tracking'],
                ]),
                'type' => 'array',
                'group' => 'ui',
                'label' => 'Hesap Menü Öğeleri',
                'description' => 'Footer hesap menüsündeki bağlantılar',
                'is_public' => true,
            ],
            [
                'key' => 'footer_info_items',
                'value' => json_encode([
                    ['title' => 'Hakkımızda', 'url' => '/about'],
                    ['title' => 'İletişim', 'url' => '/contact'],
                    ['title' => 'Gizlilik Politikası', 'url' => '/privacy'],
                    ['title' => 'Kullanım Koşulları', 'url' => '/terms'],
                    ['title' => 'İade & Değişim', 'url' => '/returns'],
                    ['title' => 'Kargo & Teslimat', 'url' => '/shipping'],
                ]),
                'type' => 'array',
                'group' => 'ui',
                'label' => 'Bilgiler Menü Öğeleri',
                'description' => 'Footer bilgiler menüsündeki bağlantılar',
                'is_public' => true,
            ],
        ];

        foreach ($footerSettings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                array_merge($settingData, [
                    'created_by' => 1,
                    'updated_by' => 1,
                ])
            );
        }

        $this->command->info('Footer ayarları başarıyla eklendi!');
    }
}