<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SiteFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'id' => 1,
                'title' => 'Ücretsiz Teslimat',
                'description' => 'Tüm siparişlerde',
                'icon' => '<svg width="33" height="27" viewBox="0 0 33 27" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.7222 1H31.5555V19.0556H10.7222V1Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.7222 7.94446H5.16667L1.00001 12.1111V19.0556H10.7222V7.94446Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M25.3055 26C23.3879 26 21.8333 24.4454 21.8333 22.5278C21.8333 20.6101 23.3879 19.0555 25.3055 19.0555C27.2232 19.0555 28.7778 20.6101 28.7778 22.5278C28.7778 24.4454 27.2232 26 25.3055 26Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.25001 26C5.33235 26 3.77778 24.4454 3.77778 22.5278C3.77778 20.6101 5.33235 19.0555 7.25001 19.0555C9.16766 19.0555 10.7222 20.6101 10.7222 22.5278C10.7222 24.4454 9.16766 26 7.25001 26Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'id' => 2,
                'title' => 'Güvenli Ödeme',
                'description' => '256-bit SSL şifreleme',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'id' => 3,
                'title' => '24/7 Destek',
                'description' => 'Müşteri hizmetleri',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'id' => 4,
                'title' => 'Hızlı Kargo',
                'description' => '1-2 iş günü teslimat',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'id' => 5,
                'title' => 'Kalite Garantisi',
                'description' => 'CE sertifikalı ürünler',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'id' => 6,
                'title' => 'Kolay İade',
                'description' => '30 gün iade garantisi',
                'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>',
                'is_active' => true,
                'sort_order' => 6
            ]
        ];

        $setting = Setting::updateOrCreate(
            ['key' => 'site_features'],
            [
                'type' => 'json',
                'group' => 'ui',
                'label' => 'Site Özellikleri',
                'description' => 'Ana sayfada gösterilecek site özelliklerinin listesi',
                'is_public' => true,
                'is_encrypted' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );
        
        // Type'ı ayarladıktan sonra value'yu set et
        $setting->value = $features;
        $setting->save();

        $this->command->info('✅ Site özellikleri ayarı oluşturuldu: ' . count($features) . ' özellik eklendi.');
    }
}