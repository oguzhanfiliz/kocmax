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
        
        DB::table('products')->where('name', 'like', 'Test Ürün %')->delete();
        
        // Sadece 3 ürün oluşturalım
        for ($i = 0; $i < 3; $i++) {
            $this->command->info("Ürün " . ($i + 1) . "/3 oluşturuluyor...");
            
            // Manuel olarak ürün verilerini oluştur (Factory kullanmadan)
            $productData = [
                'name' => 'Test Ürün ' . ($i + 1),
                'slug' => 'test-urun-' . ($i + 1) . '-' . uniqid(),
                'description' => 'Test açıklama ' . ($i + 1),
                'sku' => 'TST-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'price' => 100.00,
                'stock' => 50,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Veritabanına doğrudan insert yaparak model events'lerini bypass et
            $productId = DB::table('products')->insertGetId($productData);
            
            // Kategori ilişkisini de doğrudan ekle
            DB::table('product_categories')->insert([
                'product_id' => $productId,
                'category_id' => $categories->first(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->command->info("Ürün oluşturuldu: Test Ürün " . ($i + 1));
            
            // Garbage collection'ı zorla
            gc_collect_cycles();
        }

        DB::connection()->enableQueryLog();
        
        $this->command->info("ProductSeeder tamamlandı! 3 ürün oluşturuldu.");
    }
}
