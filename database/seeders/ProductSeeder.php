<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Önce kategoriler var mı kontrol et
        $categories = Category::whereNotNull('parent_id')->pluck('id');
        
        if ($categories->isEmpty()) {
            $this->command->warn('Alt kategori bulunamadı, ürünler oluşturulamadı. Lütfen önce CategorySeeder\'ı çalıştırın.');
            return;
        }

        $this->command->info("Kategori sayısı: " . $categories->count());
        
        // Bellek kullanımını azaltmak için batch processing kullan
        DB::connection()->disableQueryLog();
        
        // Mevcut seeder ürünlerini temizle (ISG- ile başlayan SKU'lar)
        $this->command->info("Mevcut seeder ürünleri temizleniyor...");
        
        // Seeder ürünlerini tespit et
        $seederProductIds = DB::table('products')
            ->where('sku', 'like', 'ISG-%')
            ->pluck('id');

        if ($seederProductIds->isNotEmpty()) {
            // İlişkili tabloları temizle
            DB::table('product_categories')->whereIn('product_id', $seederProductIds)->delete();
            DB::table('product_variants')->whereIn('product_id', $seederProductIds)->delete();
            DB::table('products')->whereIn('id', $seederProductIds)->delete();
            
            $this->command->info("Temizlenen ürün sayısı: " . $seederProductIds->count());
        }
        
        // Test ürünlerini de temizle
        DB::table('product_categories')->whereIn('product_id', function($query) {
            $query->select('id')->from('products')->where('name', 'like', 'Test Ürün %');
        })->delete();
        
        DB::table('product_variants')->whereIn('product_id', function($query) {
            $query->select('id')->from('products')->where('name', 'like', 'Test Ürün %');
        })->delete();
        
        DB::table('products')->where('name', 'like', 'Test Ürün %')->delete();
        
        // İSG sektörü için Türkçe örnek ürünler - Fiyatlandırma Sistemi Uyumlu
        $productData = [
            [
                'name' => 'Profesyonel Güvenlik Botu',
                'slug' => 'profesyonel-guvenlik-botu',
                'description' => 'En İleri teknoloji ile üretilmiş güvenlik botu. S3 sınıfı koruma, çelik burun, antistatik taban ve su geçirmez deri üst yapı ile endüstriyel alanlarda maksimum güvenlik sağlar. CE sertifikalıdır.',
                'short_description' => 'S3 sınıfı, çelik burun, antistatik, su geçirmez',
                'sku' => 'ISG-BOOT-PRO-001',
                'base_price' => 380.00,
                'brand' => 'TürkGüvenlik',
                'model' => 'TG-PRO-2024',
                'material' => 'Buffalo Deri + Çelik',
                'gender' => 'unisex',
                'safety_standard' => 'EN ISO 20345:2011 S3',
                'weight' => 1.35,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Siyah', 'size' => '39', 'stock' => 12, 'price' => 380.00],
                    ['color' => 'Siyah', 'size' => '40', 'stock' => 25, 'price' => 380.00],
                    ['color' => 'Siyah', 'size' => '41', 'stock' => 30, 'price' => 380.00],
                    ['color' => 'Siyah', 'size' => '42', 'stock' => 28, 'price' => 380.00],
                    ['color' => 'Siyah', 'size' => '43', 'stock' => 22, 'price' => 380.00],
                    ['color' => 'Siyah', 'size' => '44', 'stock' => 18, 'price' => 380.00],
                    ['color' => 'Kahverengi', 'size' => '40', 'stock' => 15, 'price' => 395.00],
                    ['color' => 'Kahverengi', 'size' => '41', 'stock' => 18, 'price' => 395.00],
                    ['color' => 'Kahverengi', 'size' => '42', 'stock' => 14, 'price' => 395.00],
                ]
            ],
            [
                'name' => 'Yüksek Performans Kesik Dirençli Eldiven',
                'slug' => 'yuksek-performans-kesik-direncli-eldiven',
                'description' => 'Profesyonel kullanım için üretilmiş Level 5 kesik dirençli güvenlik eldiveni. HPPE fiber + nitril kaplama ile maksimum koruma ve kavrama gücü sağlar. Otomotiv, metal işçiliği ve cam sektörü için idealdir.',
                'short_description' => 'Level 5 kesik direnci, nitril kaplama, yüksek kavrama',
                'sku' => 'ISG-ELDIV-KES-002',
                'base_price' => 65.00,
                'brand' => 'ElGüvenlik',
                'model' => 'EG-CUT5-2024',
                'material' => 'HPPE Fiber + Nitril Kaplama',
                'gender' => 'unisex',
                'safety_standard' => 'EN 388:2016 (4543X)',
                'weight' => 0.08,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Gri-Siyah', 'size' => 'XS', 'stock' => 30, 'price' => 65.00],
                    ['color' => 'Gri-Siyah', 'size' => 'S', 'stock' => 45, 'price' => 65.00],
                    ['color' => 'Gri-Siyah', 'size' => 'M', 'stock' => 60, 'price' => 65.00],
                    ['color' => 'Gri-Siyah', 'size' => 'L', 'stock' => 55, 'price' => 65.00],
                    ['color' => 'Gri-Siyah', 'size' => 'XL', 'stock' => 40, 'price' => 65.00],
                    ['color' => 'Mavi-Gri', 'size' => 'M', 'stock' => 35, 'price' => 68.00],
                    ['color' => 'Mavi-Gri', 'size' => 'L', 'stock' => 30, 'price' => 68.00],
                ]
            ],
            [
                'name' => 'Endüstriyel Güvenlik Kaskı',
                'slug' => 'endustriyel-guvenlik-kaski',
                'description' => 'Ultra dayanıklı ABS malzemeden üretilmiş endüstriyel güvenlik kaskı. Elektriksel yalıtım (1000V), 360° ayarlanabilir baş bandı, ter emici iç döşeme ve havalandırma sistemi ile konfor sağlar.',
                'short_description' => 'ABS malzeme, 1000V yalıtım, ayarlanabilir, havalandırmalı',
                'sku' => 'ISG-KASK-END-003',
                'base_price' => 125.00,
                'brand' => 'KaskGüvenlik',
                'model' => 'KG-ENDO-2024',
                'material' => 'ABS Plastik + PE İç Döşeme',
                'gender' => 'unisex',
                'safety_standard' => 'EN 397:2012 + EN 50365:2002',
                'weight' => 0.42,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Beyaz', 'size' => 'Ayarlanabilir', 'stock' => 35, 'price' => 125.00],
                    ['color' => 'Sarı', 'size' => 'Ayarlanabilir', 'stock' => 40, 'price' => 125.00],
                    ['color' => 'Mavi', 'size' => 'Ayarlanabilir', 'stock' => 30, 'price' => 125.00],
                    ['color' => 'Kırmızı', 'size' => 'Ayarlanabilir', 'stock' => 25, 'price' => 132.00],
                    ['color' => 'Turuncu', 'size' => 'Ayarlanabilir', 'stock' => 20, 'price' => 132.00],
                ]
            ],
            [
                'name' => 'Yüksek Görünürlük Reflektörlü İş Yelegi',
                'slug' => 'yuksek-gorunurluk-reflektorlu-is-yelegi',
                'description' => 'Class 2 yüksek görünürlük güvenlik yelegi. Gece ve gündüz maksimum görünürlük için 3M Scotchlite reflektörlü şeritler, nefes alabilen polyester kumaş ve çoklu cep sistemi ile profesyonel kullanım için tasarlandı.',
                'short_description' => 'Class 2, 3M reflektör, nefes alabilir, çoklu cep',
                'sku' => 'ISG-YELEK-REF-004',
                'base_price' => 55.00,
                'brand' => 'GörünürlükMax',
                'model' => 'GM-CLASS2-2024',
                'material' => 'Polyester Mesh + 3M Reflektör',
                'gender' => 'unisex',
                'safety_standard' => 'EN ISO 20471:2013 Class 2',
                'weight' => 0.18,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Turuncu-Gri', 'size' => 'S', 'stock' => 45, 'price' => 55.00],
                    ['color' => 'Turuncu-Gri', 'size' => 'M', 'stock' => 60, 'price' => 55.00],
                    ['color' => 'Turuncu-Gri', 'size' => 'L', 'stock' => 55, 'price' => 55.00],
                    ['color' => 'Turuncu-Gri', 'size' => 'XL', 'stock' => 45, 'price' => 55.00],
                    ['color' => 'Turuncu-Gri', 'size' => 'XXL', 'stock' => 25, 'price' => 58.00],
                    ['color' => 'Sarı-Gri', 'size' => 'M', 'stock' => 35, 'price' => 58.00],
                    ['color' => 'Sarı-Gri', 'size' => 'L', 'stock' => 30, 'price' => 58.00],
                ]
            ],
            [
                'name' => 'Anti-Fog Güvenlik Gözlüğü',
                'slug' => 'anti-fog-guvenlik-gozlugu',
                'description' => 'Gelişmiş anti-fog teknolojili güvenlik gözlüğü. Polikarbonat lens, UV400 koruma, çizilmez coating ve esnek yan koruma ile endüstriyel ortamlarda tam göz koruması sağlar. Gözlük kullanıcıları için uyumludur.',
                'short_description' => 'Anti-fog, UV400, çizilmez, gözlük uyumlu',
                'sku' => 'ISG-GOZLUK-AF-005',
                'base_price' => 42.00,
                'brand' => 'NetGörüş',
                'model' => 'NG-ANTIFOG-2024',
                'material' => 'Polikarbonat + TPU Çerçeve',
                'gender' => 'unisex',
                'safety_standard' => 'EN 166:2001 (1 F)',
                'weight' => 0.035,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Şeffaf', 'size' => 'Ayarlanabilir', 'stock' => 120, 'price' => 42.00],
                    ['color' => 'Füme', 'size' => 'Ayarlanabilir', 'stock' => 85, 'price' => 45.00],
                    ['color' => 'Sarı', 'size' => 'Ayarlanabilir', 'stock' => 65, 'price' => 45.00],
                    ['color' => 'Mavi Aynalı', 'size' => 'Ayarlanabilir', 'stock' => 40, 'price' => 48.00],
                ]
            ],
            [
                'name' => 'Profesyonel Solunum Maskesi N95',
                'slug' => 'profesyonel-solunum-maskesi-n95',
                'description' => 'FFP2 / N95 standardında tek kullanımlık solunum maskesi. %95 partikül filtrasyon, ergonomik tasarım, burun tellisi ve elastik kulak askıları ile konforlu ve güvenli kullanım sağlar.',
                'short_description' => 'FFP2/N95, %95 filtrasyon, ergonomik, tek kullanımlık',
                'sku' => 'ISG-MASKE-N95-006',
                'base_price' => 8.50,
                'brand' => 'SolunumKor',
                'model' => 'SK-N95-2024',
                'material' => 'Nonwoven + Meltblown Filter',
                'gender' => 'unisex',
                'safety_standard' => 'EN 149:2001+A1:2009 FFP2',
                'weight' => 0.008,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Beyaz', 'size' => 'Standart', 'stock' => 500, 'price' => 8.50],
                    ['color' => 'Mavi', 'size' => 'Standart', 'stock' => 300, 'price' => 8.50],
                ]
            ],
            [
                'name' => 'Dielektrik İş Eldiveni',
                'slug' => 'dielektrik-is-eldiveni',
                'description' => 'Elektrik işleri için özel üretilmiş dielektrik güvenlik eldiveni. 1000V AC / 1500V DC voltaj koruması, lateks kauçuk malzeme ve ergonomik kesim ile elektrikçiler için güvenli çalışma ortamı sağlar.',
                'short_description' => '1000V koruma, lateks kauçuk, elektrikçi eldiveni',
                'sku' => 'ISG-ELDIV-DIE-007',
                'base_price' => 95.00,
                'brand' => 'ElektrikKor',
                'model' => 'EK-1000V-2024',
                'material' => 'Natural Lateks Kauçuk',
                'gender' => 'unisex',
                'safety_standard' => 'EN 60903:2003 Class 00',
                'weight' => 0.15,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Turuncu', 'size' => '8', 'stock' => 20, 'price' => 95.00],
                    ['color' => 'Turuncu', 'size' => '9', 'stock' => 25, 'price' => 95.00],
                    ['color' => 'Turuncu', 'size' => '10', 'stock' => 30, 'price' => 95.00],
                    ['color' => 'Turuncu', 'size' => '11', 'stock' => 22, 'price' => 95.00],
                    ['color' => 'Kırmızı', 'size' => '9', 'stock' => 15, 'price' => 98.00],
                    ['color' => 'Kırmızı', 'size' => '10', 'stock' => 18, 'price' => 98.00],
                ]
            ]
        ];
        
        foreach ($productData as $index => $data) {
            $this->command->info("Ürün " . ($index + 1) . "/" . count($productData) . " oluşturuluyor: " . $data['name']);
            
            // Variants verilerini ayır
            $variants = $data['variants'];
            unset($data['variants']);
            
            // Benzersiz SKU kontrolü
            $baseSku = $data['sku'];
            $sku = $baseSku;
            $counter = 1;
            
            while (DB::table('products')->where('sku', $sku)->exists()) {
                $sku = $baseSku . '-' . $counter;
                $counter++;
                $this->command->warn("SKU zaten mevcut, yeni SKU: " . $sku);
            }
            
            $data['sku'] = $sku;
            
            // Timestamps ekle
            $data['created_at'] = now();
            $data['updated_at'] = now();
            
            // Ürünü oluştur
            $productId = DB::table('products')->insertGetId($data);
            
            // Kategori ilişkisini ekle
            DB::table('product_categories')->insert([
                'product_id' => $productId,
                'category_id' => $categories->first(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Varyantları oluştur
            foreach ($variants as $variantIndex => $variant) {
                // Benzersiz SKU oluştur
                $baseVariantSku = $data['sku'] . '-' . strtoupper(substr($variant['color'], 0, 3)) . '-' . str_replace(' ', '', $variant['size']);
                $variantSku = $baseVariantSku;
                $counter = 1;
                
                // SKU benzersizliğini kontrol et
                while (DB::table('product_variants')->where('sku', $variantSku)->exists()) {
                    $variantSku = $baseVariantSku . '-' . $counter;
                    $counter++;
                }
                
                $variantData = [
                    'product_id' => $productId,
                    'name' => $variant['color'] . ' - ' . $variant['size'],
                    'sku' => $variantSku,
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'color' => $variant['color'],
                    'size' => $variant['size'],
                    'is_active' => true,
                    'is_default' => $variantIndex === 0,
                    'sort_order' => $variantIndex,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                DB::table('product_variants')->insert($variantData);
            }
            
            $this->command->info("Ürün oluşturuldu: " . $data['name'] . " (" . count($variants) . " varyant)");
            
            // Garbage collection'ı zorla
            gc_collect_cycles();
        }

        DB::connection()->enableQueryLog();
        
        $totalProducts = count($productData);
        $totalVariants = collect($productData)->sum(function($product) {
            return count($product['variants']);
        });
        
        $this->command->info("🎉 ProductSeeder tamamlandı!");
        $this->command->info("📦 Toplam " . $totalProducts . " ürün oluşturuldu");
        $this->command->info("🎨 Toplam " . $totalVariants . " varyant oluşturuldu");
        $this->command->info("⭐ Featured ürünler: " . collect($productData)->where('is_featured', true)->count());
        $this->command->info("💰 Ortalama fiyat: " . number_format(collect($productData)->avg('base_price'), 2) . " ₺");
    }
}