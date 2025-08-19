<?php

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
            // Site Bilgileri (General)
            [
                'key' => 'site_title',
                'value' => 'B2B/B2C E-Ticaret Platformu',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site Başlığı',
                'description' => 'Sitenin ana başlığı, tarayıcı sekmesinde ve arama motorlarında görünür',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'site_description',
                'value' => 'İş güvenliği ve koruyucu ekipman ürünlerinde Türkiye\'nin lider B2B/B2C e-ticaret platformu',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site Açıklaması',
                'description' => 'Site hakkında kısa açıklama, SEO için önemli',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site Logosu',
                'description' => 'Sitenin ana logosu',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // İletişim Bilgileri (Contact)
            [
                'key' => 'contact_phone',
                'value' => '+90 555 123 4567',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Telefon Numarası',
                'description' => 'Müşteri hizmetleri telefon numarası',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@mutfakyapim.net',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'E-posta Adresi',
                'description' => 'Genel iletişim e-posta adresi',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Atatürk Caddesi No:123, Kadıköy/İstanbul',
                'type' => 'string',
                'group' => 'contact',
                'label' => 'Adres',
                'description' => 'Şirket fiziksel adresi',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // Şirket Bilgileri (Company)
            [
                'key' => 'company_name',
                'value' => 'Mutfak Yapım Sanayi ve Ticaret Ltd. Şti.',
                'type' => 'string',
                'group' => 'company',
                'label' => 'Şirket Adı',
                'description' => 'Resmi şirket unvanı',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // Sosyal Medya (Social)
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/mutfakyapim',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Facebook Sayfası',
                'description' => 'Facebook sayfa linki',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/mutfakyapim',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Instagram Sayfası',
                'description' => 'Instagram profil linki',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // Görünüm Ayarları (UI)
            [
                'key' => 'theme_color',
                'value' => '#3b82f6',
                'type' => 'string',
                'group' => 'ui',
                'label' => 'Ana Tema Rengi',
                'description' => 'Sitenin ana renk teması (hex kod)',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'products_per_page',
                'value' => '24',
                'type' => 'integer',
                'group' => 'ui',
                'label' => 'Sayfa Başına Ürün',
                'description' => 'Ürün listelerinde sayfa başına gösterilecek ürün sayısı',
                'is_public' => true,
                'is_encrypted' => false,
            ],

            // Copyright bilgisi
            [
                'key' => 'copyright_text',
                'value' => '© 2024 Mutfak Yapım. Tüm hakları saklıdır.',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Copyright Metni',
                'description' => 'Site altında gösterilecek copyright metni',
                'is_public' => true,
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }
    }
}