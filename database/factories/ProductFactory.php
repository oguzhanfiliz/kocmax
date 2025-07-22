<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = "Test Ürün " . $this->faker->randomNumber(3);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Test açıklama',
            'sku' => 'PRD-' . date('ymd') . '-' . strtoupper(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ')[0] . str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ')[1] . str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ')[2]),
            'base_price' => 100.00,
            'is_active' => true,
            'is_featured' => false,
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
