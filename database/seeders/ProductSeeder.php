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
        // Kategorileri al ve kontrol et
        $categories = Category::with('parent')->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('Kategori bulunamadı, ürünler oluşturulamadı. Lütfen önce CategorySeeder\'ı çalıştırın.');
            return;
        }

        $this->command->info("Toplam kategori sayısı: " . $categories->count());
        
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
        
        // Kategori bazlı ürün tanımları
        $categoryProducts = $this->getCategoryBasedProducts($categories);
        
        $totalProductsToCreate = collect($categoryProducts)->sum(function($products) {
            return count($products);
        });
        
        $this->command->info("Her kategori için ürün oluşturuluyor. Toplam: {$totalProductsToCreate} ürün");
        
        $productCounter = 0;
        
        foreach ($categoryProducts as $categoryName => $productData) {
            $category = $categories->firstWhere('name', $categoryName);
            
            if (!$category) {
                $this->command->warn("Kategori bulunamadı: {$categoryName}");
                continue;
            }
            
            $this->command->info("📂 {$categoryName} kategorisi için " . count($productData) . " ürün oluşturuluyor...");
            
            foreach ($productData as $data) {
                $productCounter++;
                $this->command->info("  Ürün {$productCounter}/{$totalProductsToCreate}: " . $data['name']);
                
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
                }
                
                $data['sku'] = $sku;
                $data['created_at'] = now();
                $data['updated_at'] = now();
                
                // Ürünü oluştur
                $productId = DB::table('products')->insertGetId($data);
                
                // Kategori ilişkisini ekle
                DB::table('product_categories')->insert([
                    'product_id' => $productId,
                    'category_id' => $category->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Varyantları oluştur
                foreach ($variants as $variantIndex => $variant) {
                    // Türkçe karakterleri temizle
                    $cleanColor = $this->cleanTurkishChars($variant['color']);
                    $cleanSize = $this->cleanTurkishChars($variant['size']);
                    
                    $baseVariantSku = $data['sku'] . '-' . strtoupper(substr($cleanColor, 0, 3)) . '-' . str_replace(' ', '', $cleanSize);
                    $variantSku = $baseVariantSku;
                    $counter = 1;
                    
                    while (DB::table('product_variants')->where('sku', $variantSku)->exists()) {
                        $variantSku = $baseVariantSku . '-' . $counter;
                        $counter++;
                    }
                    
                    $variantData = [
                        'product_id' => $productId,
                        'name' => $variant['color'] . ' - ' . $variant['size'],
                        'sku' => $variantSku,
                        'price' => $variant['price'],
                        'source_currency' => 'TRY',
                        'source_price' => $variant['price'],
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
                
                gc_collect_cycles();
            }
            
            $this->command->info("  ✅ {$categoryName} kategorisi tamamlandı!");
        }

        DB::connection()->enableQueryLog();
        
        $totalVariants = collect($categoryProducts)->flatten(1)->sum(function($product) {
            return count($product['variants']);
        });
        
        $this->command->info("🎉 ProductSeeder tamamlandı!");
        $this->command->info("📦 Toplam {$productCounter} ürün oluşturuldu");
        $this->command->info("🎨 Toplam {$totalVariants} varyant oluşturuldu");
        $this->command->info("📂 " . count($categoryProducts) . " kategoriye ürün eklendi");
    }
    
    private function getCategoryBasedProducts($categories)
    {
        return [
            // İş Kıyafetleri Ana Kategorisi Ürünleri
            'İş Kıyafetleri' => [
                [
                    'name' => 'Profesyonel İş Tulumu',
                    'slug' => 'profesyonel-is-tulumu',
                    'description' => 'Dayanıklı polyester-pamuk karışımı kumaştan üretilmiş profesyonel iş tulumu. Çoklu cep sistemi, reflektörlü şeritler ve ergonomik kesim ile endüstriyel çalışma ortamları için idealdir.',
                    'short_description' => 'Dayanıklı kumaş, çoklu cep, reflektörlü şerit',
                    'sku' => 'ISG-TULUM-PRO-001',
                    'base_currency' => 'TRY',
                    'base_price' => 285.00,
                    'brand' => 'İşKıyafet',
                    'model' => 'IK-TULUM-2024',
                    'material' => '65% Polyester, 35% Pamuk',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN ISO 11612',
                    'weight' => 0.85,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Lacivert', 'size' => 'S', 'stock' => 15, 'price' => 285.00],
                        ['color' => 'Lacivert', 'size' => 'M', 'stock' => 25, 'price' => 285.00],
                        ['color' => 'Lacivert', 'size' => 'L', 'stock' => 30, 'price' => 285.00],
                        ['color' => 'Lacivert', 'size' => 'XL', 'stock' => 20, 'price' => 285.00],
                        ['color' => 'Gri', 'size' => 'M', 'stock' => 18, 'price' => 295.00],
                        ['color' => 'Gri', 'size' => 'L', 'stock' => 22, 'price' => 295.00],
                    ]
                ]
            ],
            
            // Reflektörlü Yelekler Alt Kategorisi
            'Reflektörlü Yelekler' => [
                [
                    'name' => 'Yüksek Görünürlük Reflektörlü İş Yelegi',
                    'slug' => 'yuksek-gorunurluk-reflektorlu-is-yelegi',
                    'description' => 'Class 2 yüksek görünürlük güvenlik yelegi. Gece ve gündüz maksimum görünürlük için 3M Scotchlite reflektörlü şeritler, nefes alabilen polyester kumaş ve çoklu cep sistemi ile profesyonel kullanım için tasarlandı.',
                    'short_description' => 'Class 2, 3M reflektör, nefes alabilir, çoklu cep',
                    'sku' => 'ISG-YELEK-REF-001',
                    'base_currency' => 'TRY',
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
                        ['color' => 'Sarı-Gri', 'size' => 'M', 'stock' => 35, 'price' => 58.00],
                        ['color' => 'Sarı-Gri', 'size' => 'L', 'stock' => 30, 'price' => 58.00],
                    ]
                ]
            ],
            
            // İş Pantolonları Alt Kategorisi
            'İş Pantolonları' => [
                [
                    'name' => 'Dayanıklı Kargo İş Pantolonu',
                    'slug' => 'dayanikli-kargo-is-pantolonu',
                    'description' => 'Ağır iş koşulları için tasarlanmış dayanıklı kargo pantolonu. Çoklu cep sistemi, diz koruma pedleri ve esnek kumaş yapısı ile konfor ve dayanıklılığı bir arada sunar.',
                    'short_description' => 'Çoklu cep, diz koruması, esnek kumaş',
                    'sku' => 'ISG-PANTOLON-KARGO-001',
                    'base_currency' => 'TRY',
                    'base_price' => 165.00,
                    'brand' => 'İşKıyafet',
                    'model' => 'IK-KARGO-2024',
                    'material' => '65% Polyester, 35% Pamuk + Elastan',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN 14404',
                    'weight' => 0.55,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Lacivert', 'size' => '46', 'stock' => 25, 'price' => 165.00],
                        ['color' => 'Lacivert', 'size' => '48', 'stock' => 30, 'price' => 165.00],
                        ['color' => 'Lacivert', 'size' => '50', 'stock' => 28, 'price' => 165.00],
                        ['color' => 'Lacivert', 'size' => '52', 'stock' => 22, 'price' => 165.00],
                        ['color' => 'Gri', 'size' => '48', 'stock' => 20, 'price' => 175.00],
                        ['color' => 'Gri', 'size' => '50', 'stock' => 18, 'price' => 175.00],
                    ]
                ]
            ],
            
            // İş Ayakkabıları Ana Kategorisi
            'İş Ayakkabıları' => [
                [
                    'name' => 'Profesyonel Güvenlik Botu',
                    'slug' => 'profesyonel-guvenlik-botu',
                    'description' => 'En ileri teknoloji ile üretilmiş güvenlik botu. S3 sınıfı koruma, çelik burun, antistatik taban ve su geçirmez deri üst yapı ile endüstriyel alanlarda maksimum güvenlik sağlar.',
                    'short_description' => 'S3 sınıfı, çelik burun, antistatik, su geçirmez',
                    'sku' => 'ISG-BOOT-PRO-001',
                    'base_currency' => 'TRY',
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
                        ['color' => 'Kahverengi', 'size' => '41', 'stock' => 18, 'price' => 395.00],
                        ['color' => 'Kahverengi', 'size' => '42', 'stock' => 14, 'price' => 395.00],
                    ]
                ]
            ],
            
            // S1P Ayakkabılar Alt Kategorisi
            'S1P Ayakkabılar' => [
                [
                    'name' => 'Hafif S1P Güvenlik Ayakkabısı',
                    'slug' => 'hafif-s1p-guvenlik-ayakkabisi',
                    'description' => 'Günlük kullanım için tasarlanmış hafif S1P güvenlik ayakkabısı. Kompozit burun, metal içermeyen taban ve nefes alabilen mesh astar ile konforlu çalışma deneyimi sunar.',
                    'short_description' => 'S1P sınıfı, kompozit burun, metal içermeyen, nefes alabilir',
                    'sku' => 'ISG-AYAKKABI-S1P-001',
                    'base_currency' => 'TRY',
                    'base_price' => 245.00,
                    'brand' => 'TürkGüvenlik',
                    'model' => 'TG-S1P-2024',
                    'material' => 'Nubuk Deri + Kompozit',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN ISO 20345:2011 S1P',
                    'weight' => 0.95,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Siyah', 'size' => '39', 'stock' => 20, 'price' => 245.00],
                        ['color' => 'Siyah', 'size' => '40', 'stock' => 35, 'price' => 245.00],
                        ['color' => 'Siyah', 'size' => '41', 'stock' => 40, 'price' => 245.00],
                        ['color' => 'Siyah', 'size' => '42', 'stock' => 38, 'price' => 245.00],
                        ['color' => 'Siyah', 'size' => '43', 'stock' => 30, 'price' => 245.00],
                        ['color' => 'Gri', 'size' => '41', 'stock' => 25, 'price' => 255.00],
                        ['color' => 'Gri', 'size' => '42', 'stock' => 22, 'price' => 255.00],
                    ]
                ]
            ],
            
            // S3 Çizmeler Alt Kategorisi
            'S3 Çizmeler' => [
                [
                    'name' => 'Su Geçirmez S3 Güvenlik Çizmesi',
                    'slug' => 'su-gecirmez-s3-guvenlik-cizmesi',
                    'description' => 'Zorlu dış mekan koşulları için üretilmiş su geçirmez S3 güvenlik çizmesi. Çelik burun, çelik taban, antistatik özellik ve yüksek bilek desteği ile maksimum koruma sağlar.',
                    'short_description' => 'S3 sınıfı, su geçirmez, çelik burun ve taban, yüksek bilek',
                    'sku' => 'ISG-CIZME-S3-001',
                    'base_currency' => 'TRY',
                    'base_price' => 425.00,
                    'brand' => 'TürkGüvenlik',
                    'model' => 'TG-S3-BOOT-2024',
                    'material' => 'Su Geçirmez Deri + Çelik',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN ISO 20345:2011 S3',
                    'weight' => 1.65,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Siyah', 'size' => '39', 'stock' => 15, 'price' => 425.00],
                        ['color' => 'Siyah', 'size' => '40', 'stock' => 25, 'price' => 425.00],
                        ['color' => 'Siyah', 'size' => '41', 'stock' => 30, 'price' => 425.00],
                        ['color' => 'Siyah', 'size' => '42', 'stock' => 28, 'price' => 425.00],
                        ['color' => 'Siyah', 'size' => '43', 'stock' => 20, 'price' => 425.00],
                        ['color' => 'Kahverengi', 'size' => '41', 'stock' => 18, 'price' => 445.00],
                        ['color' => 'Kahverengi', 'size' => '42', 'stock' => 15, 'price' => 445.00],
                    ]
                ]
            ],
            
            // İş Eldivenleri Ana Kategorisi
            'İş Eldivenleri' => [
                [
                    'name' => 'Çok Amaçlı İş Eldiveni',
                    'slug' => 'cok-amacli-is-eldiveni',
                    'description' => 'Genel amaçlı kullanım için tasarlanmış dayanıklı iş eldiveni. Pamuk-polyester karışımı kumaş, PVC noktalı avuç içi ve elastik bilek bandı ile günlük iş aktiviteleri için idealdir.',
                    'short_description' => 'Çok amaçlı, PVC noktalı, elastik bilek',
                    'sku' => 'ISG-ELDIV-GENEL-001',
                    'base_currency' => 'TRY',
                    'base_price' => 25.00,
                    'brand' => 'ElGüvenlik',
                    'model' => 'EG-GENEL-2024',
                    'material' => 'Pamuk-Polyester + PVC',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN 388:2016',
                    'weight' => 0.05,
                    'is_active' => true,
                    'is_featured' => false,
                    'variants' => [
                        ['color' => 'Beyaz-Mavi', 'size' => 'S', 'stock' => 50, 'price' => 25.00],
                        ['color' => 'Beyaz-Mavi', 'size' => 'M', 'stock' => 75, 'price' => 25.00],
                        ['color' => 'Beyaz-Mavi', 'size' => 'L', 'stock' => 70, 'price' => 25.00],
                        ['color' => 'Beyaz-Mavi', 'size' => 'XL', 'stock' => 45, 'price' => 25.00],
                        ['color' => 'Gri-Siyah', 'size' => 'M', 'stock' => 40, 'price' => 28.00],
                        ['color' => 'Gri-Siyah', 'size' => 'L', 'stock' => 35, 'price' => 28.00],
                    ]
                ]
            ],
            
            // Mekanik Koruma Eldivenleri Alt Kategorisi
            'Mekanik Koruma Eldivenleri' => [
                [
                    'name' => 'Yüksek Performans Kesik Dirençli Eldiven',
                    'slug' => 'yuksek-performans-kesik-direncli-eldiven',
                    'description' => 'Profesyonel kullanım için üretilmiş Level 5 kesik dirençli güvenlik eldiveni. HPPE fiber + nitril kaplama ile maksimum koruma ve kavrama gücü sağlar. Otomotiv, metal işçiliği ve cam sektörü için idealdir.',
                    'short_description' => 'Level 5 kesik direnci, nitril kaplama, yüksek kavrama',
                    'sku' => 'ISG-ELDIV-KES-001',
                    'base_currency' => 'TRY',
                    'base_price' => 65.00,
                    'brand' => 'ElGüvenlik',
                    'model' => 'EG-CUT5-2024',
                    'material' => 'HPPE Fiber + Nitril Kaplama',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN 388:2016 (4543X)',
                    'weight' => 0.08,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Gri-Siyah', 'size' => 'XS', 'stock' => 30, 'price' => 65.00],
                        ['color' => 'Gri-Siyah', 'size' => 'S', 'stock' => 45, 'price' => 65.00],
                        ['color' => 'Gri-Siyah', 'size' => 'M', 'stock' => 60, 'price' => 65.00],
                        ['color' => 'Gri-Siyah', 'size' => 'L', 'stock' => 55, 'price' => 65.00],
                        ['color' => 'Gri-Siyah', 'size' => 'XL', 'stock' => 40, 'price' => 65.00],
                        ['color' => 'Mavi-Gri', 'size' => 'M', 'stock' => 35, 'price' => 68.00],
                        ['color' => 'Mavi-Gri', 'size' => 'L', 'stock' => 30, 'price' => 68.00],
                    ]
                ]
            ],
            
            // Kimyasal Koruma Eldivenleri Alt Kategorisi
            'Kimyasal Koruma Eldivenleri' => [
                [
                    'name' => 'Kimyasal Dirençli Nitril Eldiven',
                    'slug' => 'kimyasal-direncli-nitril-eldiven',
                    'description' => 'Kimyasal maddelere karşı yüksek direnç gösteren nitril eldiven. Tek kullanımlık, pudrasız ve alerjik reaksiyon riski düşük formülasyon ile laboratuvar ve endüstriyel kullanım için idealdir.',
                    'short_description' => 'Kimyasal dirençli, nitril, tek kullanımlık, pudrasız',
                    'sku' => 'ISG-ELDIV-NITRIL-001',
                    'base_currency' => 'TRY',
                    'base_price' => 45.00,
                    'brand' => 'ElGüvenlik',
                    'model' => 'EG-NITRIL-2024',
                    'material' => '%100 Nitril Kauçuk',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN 374-1:2016',
                    'weight' => 0.006,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Mavi', 'size' => 'S', 'stock' => 200, 'price' => 45.00],
                        ['color' => 'Mavi', 'size' => 'M', 'stock' => 300, 'price' => 45.00],
                        ['color' => 'Mavi', 'size' => 'L', 'stock' => 250, 'price' => 45.00],
                        ['color' => 'Mavi', 'size' => 'XL', 'stock' => 150, 'price' => 45.00],
                        ['color' => 'Siyah', 'size' => 'M', 'stock' => 180, 'price' => 48.00],
                        ['color' => 'Siyah', 'size' => 'L', 'stock' => 160, 'price' => 48.00],
                    ]
                ]
            ],
            
            // Kafa Koruyucular Ana Kategorisi
            'Kafa Koruyucular' => [
                [
                    'name' => 'Endüstriyel Güvenlik Kaskı',
                    'slug' => 'endustriyel-guvenlik-kaski',
                    'description' => 'Ultra dayanıklı ABS malzemeden üretilmiş endüstriyel güvenlik kaskı. Elektriksel yalıtım (1000V), 360° ayarlanabilir baş bandı, ter emici iç döşeme ve havalandırma sistemi ile konfor sağlar.',
                    'short_description' => 'ABS malzeme, 1000V yalıtım, ayarlanabilir, havalandırmalı',
                    'sku' => 'ISG-KASK-END-001',
                    'base_currency' => 'TRY',
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
                ]
            ],
            
            // Baretler Alt Kategorisi
            'Baretler' => [
                [
                    'name' => 'Hafif Polietilen Güvenlik Bareti',
                    'slug' => 'hafif-polietilen-guvenlik-bareti',
                    'description' => 'Günlük kullanım için tasarlanmış hafif polietilen güvenlik bareti. Ayarlanabilir baş bandı, ter emici iç döşeme ve çoklu havalandırma delikleri ile konforlu kullanım sağlar.',
                    'short_description' => 'Hafif polietilen, ayarlanabilir, havalandırmalı',
                    'sku' => 'ISG-BARET-PE-001',
                    'base_currency' => 'TRY',
                    'base_price' => 65.00,
                    'brand' => 'KaskGüvenlik',
                    'model' => 'KG-PE-2024',
                    'material' => 'Yüksek Yoğunluklu Polietilen',
                    'gender' => 'unisex',
                    'safety_standard' => 'EN 397:2012',
                    'weight' => 0.28,
                    'is_active' => true,
                    'is_featured' => true,
                    'variants' => [
                        ['color' => 'Beyaz', 'size' => 'Ayarlanabilir', 'stock' => 50, 'price' => 65.00],
                        ['color' => 'Sarı', 'size' => 'Ayarlanabilir', 'stock' => 60, 'price' => 65.00],
                        ['color' => 'Mavi', 'size' => 'Ayarlanabilir', 'stock' => 45, 'price' => 65.00],
                        ['color' => 'Kırmızı', 'size' => 'Ayarlanabilir', 'stock' => 35, 'price' => 68.00],
                        ['color' => 'Turuncu', 'size' => 'Ayarlanabilir', 'stock' => 30, 'price' => 68.00],
                        ['color' => 'Yeşil', 'size' => 'Ayarlanabilir', 'stock' => 25, 'price' => 68.00],
                    ]
                ]
            ]
        ];
    }
    
    private function cleanTurkishChars($text)
    {
        $turkishChars = [
            'ç' => 'c', 'Ç' => 'C',
            'ğ' => 'g', 'Ğ' => 'G',
            'ı' => 'i', 'I' => 'I',
            'İ' => 'I', 'i' => 'i',
            'ö' => 'o', 'Ö' => 'O',
            'ş' => 's', 'Ş' => 'S',
            'ü' => 'u', 'Ü' => 'U'
        ];
        
        return str_replace(array_keys($turkishChars), array_values($turkishChars), $text);
    }
}