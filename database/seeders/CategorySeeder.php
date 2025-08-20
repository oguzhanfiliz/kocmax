<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Ana Kategoriler - bazıları öne çıkarılan olarak işaretlendi
        $categories = [
            [
                'name' => 'İş Kıyafetleri',
                'is_featured' => true,
                'is_in_menu' => true,
                'icon' => 'heroicon-o-user-group',
                'sort_order' => 1
            ],
            [
                'name' => 'İş Ayakkabıları',
                'is_featured' => true,
                'is_in_menu' => true,
                'icon' => 'heroicon-o-shoe',
                'sort_order' => 2
            ],
            [
                'name' => 'İş Eldivenleri',
                'is_featured' => false,
                'is_in_menu' => true,
                'icon' => 'heroicon-o-hand-raised',
                'sort_order' => 3
            ],
            [
                'name' => 'Kafa Koruyucular',
                'is_featured' => true,
                'is_in_menu' => true,
                'icon' => 'heroicon-o-shield-check',
                'sort_order' => 4
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryData['name'])],
                [
                    'name' => $categoryData['name'],
                    'is_featured' => $categoryData['is_featured'],
                    'is_in_menu' => $categoryData['is_in_menu'],
                    'icon' => $categoryData['icon'],
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // Alt Kategoriler
        $subCategories = [
            'İş Kıyafetleri' => [
                [
                    'name' => 'Reflektörlü Yelekler',
                    'is_featured' => true,
                    'is_in_menu' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'İş Pantolonları',
                    'is_featured' => false,
                    'is_in_menu' => true,
                    'sort_order' => 2
                ]
            ],
            'İş Ayakkabıları' => [
                [
                    'name' => 'S1P Ayakkabılar',
                    'is_featured' => true,
                    'is_in_menu' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'S3 Çizmeler',
                    'is_featured' => false,
                    'is_in_menu' => true,
                    'sort_order' => 2
                ]
            ],
            'İş Eldivenleri' => [
                [
                    'name' => 'Mekanik Koruma Eldivenleri',
                    'is_featured' => false,
                    'is_in_menu' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Kimyasal Koruma Eldivenleri',
                    'is_featured' => false,
                    'is_in_menu' => true,
                    'sort_order' => 2
                ]
            ],
            'Kafa Koruyucular' => [
                [
                    'name' => 'Baretler',
                    'is_featured' => false,
                    'is_in_menu' => true,
                    'sort_order' => 1
                ]
            ],
        ];

        foreach ($subCategories as $parentName => $children) {
            $parentCategory = Category::where('name', $parentName)->first();
            if ($parentCategory) {
                foreach ($children as $childData) {
                    Category::firstOrCreate(
                        [
                            'slug' => Str::slug($childData['name']),
                            'parent_id' => $parentCategory->id
                        ],
                        [
                            'name' => $childData['name'],
                            'is_featured' => $childData['is_featured'],
                            'is_in_menu' => $childData['is_in_menu'],
                            'sort_order' => $childData['sort_order'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('Kategoriler oluşturuldu. Öne çıkarılan kategoriler: İş Kıyafetleri, İş Ayakkabıları, Kafa Koruyucular, Reflektörlü Yelekler, S1P Ayakkabılar');
    }
}
