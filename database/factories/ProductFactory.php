<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Reflektörlü İkaz Yeleği', 'Çelik Burunlu İş Ayakkabısı', 'Darbe Emici Baret',
            'Kaynakçı Eldiveni', 'Tam Yüz Gaz Maskesi', 'Paraşüt Tipi Emniyet Kemeri',
            'İş Güvenliği Gözlüğü', 'Kaymaz Tabanlı Çizme', 'Kışlık İş Parkası'
        ]) . ' ' . $this->faker->unique()->word;

        return [
            'name' => $name,
            // Slug model event'inde oluşturulacak
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 150, 2500),
            'stock' => $this->faker->numberBetween(10, 200),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(25),
        ];
    }

    public function hasImages(int $count = 1)
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            $product->images()->createMany(
                \App\Models\ProductImage::factory()->count($count)->make()->toArray()
            );
        });
    }

    public function hasVariants(int $count = 1)
    {
        // Bu özellik, VariantGeneratorService ile daha mantıklı bir şekilde ele alınacak.
        // Şimdilik seeder içinde manuel olarak yönetilecek.
        return $this;
    }
}
