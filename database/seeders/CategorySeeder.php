<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Ana Kategoriler
        $categories = [
            'İş Kıyafetleri',
            'İş Ayakkabıları',
            'İş Eldivenleri',
            'Kafa Koruyucular',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );
        }

        // Alt Kategoriler
        $subCategories = [
            'İş Kıyafetleri' => ['Reflektörlü Yelekler', 'İş Pantolonları'],
            'İş Ayakkabıları' => ['S1P Ayakkabılar', 'S3 Çizmeler'],
            'İş Eldivenleri' => ['Mekanik Koruma Eldivenleri', 'Kimyasal Koruma Eldivenleri'],
            'Kafa Koruyucular' => ['Baretler'],
        ];

        foreach ($subCategories as $parentName => $children) {
            $parentCategory = Category::where('name', $parentName)->first();
            if ($parentCategory) {
                foreach ($children as $childName) {
                    Category::firstOrCreate(
                        ['slug' => Str::slug($childName), 'parent_id' => $parentCategory->id],
                        ['name' => $childName]
                    );
                }
            }
        }
    }
}
