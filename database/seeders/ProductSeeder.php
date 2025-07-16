<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereNotNull('parent_id')->get();

        if ($categories->isEmpty()) {
            $this->command->warn('Alt kategori bulunamadı, ürünler oluşturulamadı. Lütfen önce CategorySeeder\'ı çalıştırın.');
            return;
        }

        Product::factory()
            ->count(50)
            ->hasImages(3)
            ->create()
            ->each(function ($product) use ($categories) {
                // Her ürüne rastgele 1 veya 2 alt kategori ata
                $product->categories()->attach(
                    $categories->random(rand(1, 2))->pluck('id')->toArray()
                );
            });
    }
}
