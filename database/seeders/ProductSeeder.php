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
        
        // Mevcut test ürünlerini temizle
        DB::table('product_categories')->whereIn('product_id', function($query) {
            $query->select('id')->from('products')->where('name', 'like', 'Test Ürün %');
        })->delete();
        
        DB::table('product_variants')->whereIn('product_id', function($query) {
            $query->select('id')->from('products')->where('name', 'like', 'Test Ürün %');
        })->delete();
        
        DB::table('products')->where('name', 'like', 'Test Ürün %')->delete();
        
        // İSG sektörü için örnek ürünler
        $productData = [
            [
                'name' => 'Güvenlik Botu Pro',
                'slug' => 'guvenlik-botu-pro',
                'description' => 'Yüksek kaliteli güvenlik botu. Çelik burunlu, kaymaz tabanı ile maksimum güvenlik sağlar.',
                'short_description' => 'Çelik burunlu, kaymaz taban',
                'sku' => 'GUV-BOOT-001',
                'base_price' => 250.00,
                'brand' => 'SafetyPro',
                'model' => 'SP-2024',
                'material' => 'Deri',
                'gender' => 'unisex',
                'safety_standard' => 'EN ISO 20345:2011',
                'weight' => 1.2,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Siyah', 'size' => '40', 'stock' => 15, 'price' => 250.00],
                    ['color' => 'Siyah', 'size' => '41', 'stock' => 20, 'price' => 250.00],
                    ['color' => 'Siyah', 'size' => '42', 'stock' => 18, 'price' => 250.00],
                    ['color' => 'Kahverengi', 'size' => '40', 'stock' => 10, 'price' => 260.00],
                    ['color' => 'Kahverengi', 'size' => '41', 'stock' => 12, 'price' => 260.00],
                ]
            ],
            [
                'name' => 'Güvenlik Eldiveni',
                'slug' => 'guvenlik-eldiveni',
                'description' => 'Kesik dirençli güvenlik eldiveni. Level 5 koruma seviyesi.',
                'short_description' => 'Level 5 koruma, kesik dirençli',
                'sku' => 'GUV-GLOVE-002',
                'base_price' => 45.00,
                'brand' => 'ProtectHand',
                'model' => 'PH-CUT5',
                'material' => 'HPPE + Nitrile',
                'gender' => 'unisex',
                'safety_standard' => 'EN 388:2016',
                'weight' => 0.1,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Gri', 'size' => 'S', 'stock' => 50, 'price' => 45.00],
                    ['color' => 'Gri', 'size' => 'M', 'stock' => 60, 'price' => 45.00],
                    ['color' => 'Gri', 'size' => 'L', 'stock' => 55, 'price' => 45.00],
                    ['color' => 'Gri', 'size' => 'XL', 'stock' => 40, 'price' => 45.00],
                ]
            ],
            [
                'name' => 'Güvenlik Kaskı',
                'slug' => 'guvenlik-kaski',
                'description' => 'Darbe dirençli güvenlik kaskı. Ayarlanabilir baş bandı.',
                'short_description' => 'Darbe dirençli, ayarlanabilir',
                'sku' => 'GUV-HELMET-003',
                'base_price' => 85.00,
                'brand' => 'SafeHead',
                'model' => 'SH-HARD-2024',
                'material' => 'ABS Plastik',
                'gender' => 'unisex',
                'safety_standard' => 'EN 397:2012',
                'weight' => 0.4,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    ['color' => 'Beyaz', 'size' => 'Standart', 'stock' => 25, 'price' => 85.00],
                    ['color' => 'Sarı', 'size' => 'Standart', 'stock' => 30, 'price' => 85.00],
                    ['color' => 'Mavi', 'size' => 'Standart', 'stock' => 20, 'price' => 85.00],
                    ['color' => 'Kırmızı', 'size' => 'Standart', 'stock' => 15, 'price' => 90.00],
                ]
            ],
            [
                'name' => 'Reflektörlü Yelek',
                'slug' => 'reflektorlu-yelek',
                'description' => 'Yüksek görünürlük reflektörlü güvenlik yelegi. Gece görünürlüğü artırır.',
                'short_description' => 'Yüksek görünürlük, reflektörlü',
                'sku' => 'GUV-VEST-004',
                'base_price' => 35.00,
                'brand' => 'VisibilityMax',
                'model' => 'VM-REFLECT-2024',
                'material' => 'Polyester',
                'gender' => 'unisex',
                'safety_standard' => 'EN ISO 20471:2013',
                'weight' => 0.2,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Turuncu', 'size' => 'S', 'stock' => 40, 'price' => 35.00],
                    ['color' => 'Turuncu', 'size' => 'M', 'stock' => 50, 'price' => 35.00],
                    ['color' => 'Turuncu', 'size' => 'L', 'stock' => 45, 'price' => 35.00],
                    ['color' => 'Turuncu', 'size' => 'XL', 'stock' => 35, 'price' => 35.00],
                    ['color' => 'Sarı', 'size' => 'M', 'stock' => 30, 'price' => 38.00],
                    ['color' => 'Sarı', 'size' => 'L', 'stock' => 25, 'price' => 38.00],
                ]
            ],
            [
                'name' => 'Güvenlik Gözlüğü',
                'slug' => 'guvenlik-gozlugu',
                'description' => 'Şeffaf güvenlik gözlüğü. Çizilmeye dayanıklı cam.',
                'short_description' => 'Çizilmeye dayanıklı, şeffaf',
                'sku' => 'GUV-GLASS-005',
                'base_price' => 25.00,
                'brand' => 'ClearVision',
                'model' => 'CV-SAFE-2024',
                'material' => 'Polikarbonat',
                'gender' => 'unisex',
                'safety_standard' => 'EN 166:2001',
                'weight' => 0.05,
                'is_active' => true,
                'is_featured' => false,
                'variants' => [
                    ['color' => 'Şeffaf', 'size' => 'Standart', 'stock' => 100, 'price' => 25.00],
                    ['color' => 'Füme', 'size' => 'Standart', 'stock' => 75, 'price' => 28.00],
                    ['color' => 'Sarı', 'size' => 'Standart', 'stock' => 50, 'price' => 28.00],
                ]
            ]
        ];
        
        foreach ($productData as $index => $data) {
            $this->command->info("Ürün " . ($index + 1) . "/" . count($productData) . " oluşturuluyor: " . $data['name']);
            
            // Variants verilerini ayır
            $variants = $data['variants'];
            unset($data['variants']);
            
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
                $variantData = [
                    'product_id' => $productId,
                    'name' => $variant['color'] . ' - ' . $variant['size'],
                    'sku' => $data['sku'] . '-' . strtoupper(substr($variant['color'], 0, 3)) . '-' . str_replace(' ', '', $variant['size']),
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
        
        $this->command->info("ProductSeeder tamamlandı! " . count($productData) . " ürün oluşturuldu.");
    }
}