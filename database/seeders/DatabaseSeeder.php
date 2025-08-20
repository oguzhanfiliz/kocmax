<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Database seeding başlıyor...');
        
        $seeders = [
            'İzin sistemi' => [
                PermissionSeeder::class,
                PermissionSeederForAdminRole::class,
            ],
            'Kullanıcılar' => [
                UserSeeder::class,
            ],
            'Sistem ayarları' => [
                SettingSeeder::class,
                MigrateConfigSettingsSeeder::class,
                EssentialSettingsSeeder::class,
            ],
            'Temel veriler' => [
                CurrencySeeder::class,
                CategorySeeder::class,
                SkuConfigurationSeeder::class,
                VariantTypeSeeder::class,
            ],
            'Fiyatlandırma sistemi' => [
                CustomerPricingTierSeeder::class,
                PricingRuleSeeder::class,
            ],
            'Ürünler ve işlemler' => [
                ProductSeeder::class,
                DealerApplicationSeeder::class,
                ProductReviewSeeder::class,
            ]
        ];

        foreach ($seeders as $groupName => $seederClasses) {
            $this->command->info("📁 {$groupName} seeders çalıştırılıyor...");
            
            foreach ($seederClasses as $seederClass) {
                $this->command->info("  ⏳ " . class_basename($seederClass) . " çalıştırılıyor...");
                $this->call($seederClass);
            }
            
            $this->command->info("  ✅ {$groupName} tamamlandı!");
        }
        
        $this->command->info('🎉 Tüm seeder işlemleri başarıyla tamamlandı!');
        $this->command->info('📊 Sistem özeti:');
        $this->command->info('   ⚙️  Sistem ayarları: ' . \App\Models\Setting::count());
        $this->command->info('   👥 Kullanıcılar: ' . \App\Models\User::count());
        $this->command->info('   🏢 Müşteri seviyeleri: ' . \App\Models\CustomerPricingTier::count());
        $this->command->info('   📋 Fiyat kuralları: ' . \App\Models\PricingRule::count());
        $this->command->info('   📦 Ürünler: ' . \App\Models\Product::count());
        $this->command->info('   🎨 Ürün varyantları: ' . \App\Models\ProductVariant::count());
        $this->command->info('   📂 Kategoriler: ' . \App\Models\Category::count());
        $this->command->info('   💱 Para birimleri: ' . \App\Models\Currency::count());
    }
}
