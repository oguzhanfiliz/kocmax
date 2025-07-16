<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Ana Kategoriler
        $clothing = Category::factory()->create(['name' => 'İş Kıyafetleri']);
        $shoes = Category::factory()->create(['name' => 'İş Ayakkabıları']);
        $gloves = Category::factory()->create(['name' => 'İş Eldivenleri']);
        $head = Category::factory()->create(['name' => 'Kafa Koruyucular']);

        // Alt Kategoriler
        Category::factory()->create(['name' => 'Reflektörlü Yelekler', 'parent_id' => $clothing->id]);
        Category::factory()->create(['name' => 'İş Pantolonları', 'parent_id' => $clothing->id]);

        Category::factory()->create(['name' => 'S1P Ayakkabılar', 'parent_id' => $shoes->id]);
        Category::factory()->create(['name' => 'S3 Çizmeler', 'parent_id' => $shoes->id]);

        Category::factory()->create(['name' => 'Mekanik Koruma Eldivenleri', 'parent_id' => $gloves->id]);
        Category::factory()->create(['name' => 'Kimyasal Koruma Eldivenleri', 'parent_id' => $gloves->id]);

        Category::factory()->create(['name' => 'Baretler', 'parent_id' => $head->id]);
    }
}
