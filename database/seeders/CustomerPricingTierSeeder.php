<?php

namespace Database\Seeders;

use App\Models\CustomerPricingTier;
use Illuminate\Database\Seeder;

class CustomerPricingTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // B2B Müşteri Seviyeleri
        CustomerPricingTier::create([
            'name' => 'Standart Bayi',
            'type' => 'b2b',
            'discount_percentage' => 10.00,
            'min_order_amount' => 500.00,
            'description' => 'Yeni başlayan bayiler için temel fiyatlandırma seviyesi. Minimum 500₺ siparişte %10 indirim.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Gümüş Bayi',
            'type' => 'b2b',
            'discount_percentage' => 15.00,
            'min_order_amount' => 1000.00,
            'description' => 'Düzenli alım yapan bayiler için. Minimum 1.000₺ siparişte %15 indirim ve özel kampanya fırsatları.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Altın Bayi',
            'type' => 'b2b',
            'discount_percentage' => 20.00,
            'min_order_amount' => 2500.00,
            'description' => 'Premium bayiler için gelişmiş fiyatlandırma. Minimum 2.500₺ siparişte %20 indirim ve öncelikli destek.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Platin Bayi',
            'type' => 'b2b',
            'discount_percentage' => 25.00,
            'min_order_amount' => 5000.00,
            'description' => 'VIP bayiler için özel fiyatlandırma. Minimum 5.000₺ siparişte %25 indirim, ücretsiz kargo ve özel ürün erişimi.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Toptan Satış',
            'type' => 'wholesale',
            'discount_percentage' => 30.00,
            'min_order_amount' => 10000.00,
            'description' => 'Büyük hacimli alımlar için toptan satış fiyatlandırması. Minimum 10.000₺ siparişte %30 indirim.',
            'is_active' => true,
        ]);

        // B2C Müşteri Seviyeleri
        CustomerPricingTier::create([
            'name' => 'Bireysel Müşteri',
            'type' => 'b2c',
            'discount_percentage' => 0.00,
            'min_order_amount' => 0.00,
            'description' => 'Standart bireysel müşteri fiyatlandırması. Liste fiyatı üzerinden satış.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Sadık Müşteri',
            'type' => 'b2c',
            'discount_percentage' => 5.00,
            'min_order_amount' => 200.00,
            'description' => 'Düzenli alışveriş yapan bireysel müşteriler için. Minimum 200₺ siparişte %5 indirim.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'VIP Müşteri',
            'type' => 'b2c',
            'discount_percentage' => 8.00,
            'min_order_amount' => 500.00,
            'description' => 'Yüksek hacimli bireysel müşteriler için. Minimum 500₺ siparişte %8 indirim ve özel kampanyalar.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Kurumsal Bireysel',
            'type' => 'b2c',
            'discount_percentage' => 12.00,
            'min_order_amount' => 1000.00,
            'description' => 'Şirket adına bireysel alım yapanlar için. Minimum 1.000₺ siparişte %12 indirim.',
            'is_active' => true,
        ]);

        // Özel Kategoriler
        CustomerPricingTier::create([
            'name' => 'Eğitim Kurumu',
            'type' => 'b2b',
            'discount_percentage' => 18.00,
            'min_order_amount' => 750.00,
            'description' => 'Okul, üniversite ve eğitim kurumları için özel fiyatlandırma. Minimum 750₺ siparişte %18 indirim.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Devlet Kurumu',
            'type' => 'b2b',
            'discount_percentage' => 15.00,
            'min_order_amount' => 1500.00,
            'description' => 'Kamu kurumları için özel fiyatlandırma. Minimum 1.500₺ siparişte %15 indirim.',
            'is_active' => true,
        ]);

        CustomerPricingTier::create([
            'name' => 'Sağlık Kurumu',
            'type' => 'b2b',
            'discount_percentage' => 22.00,
            'min_order_amount' => 1200.00,
            'description' => 'Hastane ve sağlık kuruluşları için özel fiyatlandırma. Minimum 1.200₺ siparişte %22 indirim.',
            'is_active' => true,
        ]);

        // İnaktif Tier (Test için)
        CustomerPricingTier::create([
            'name' => 'Eski Bayi Seviyesi',
            'type' => 'b2b',
            'discount_percentage' => 20.00,
            'min_order_amount' => 1000.00,
            'description' => 'Artık kullanılmayan eski bayi seviyesi. Test amaçlı inaktif bırakıldı.',
            'is_active' => false,
        ]);

        $this->command->info('✅ ' . CustomerPricingTier::count() . ' müşteri fiyatlandırma seviyesi oluşturuldu.');
        $this->command->info('📊 B2B: ' . CustomerPricingTier::where('type', 'b2b')->count() . ' seviye');
        $this->command->info('👤 B2C: ' . CustomerPricingTier::where('type', 'b2c')->count() . ' seviye');
        $this->command->info('📦 Toptan: ' . CustomerPricingTier::where('type', 'wholesale')->count() . ' seviye');
    }
}