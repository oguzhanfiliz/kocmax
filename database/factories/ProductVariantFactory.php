<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => 'TEST-' . strtoupper($this->faker->lexify('???-???')),
            'name' => $this->faker->words(2, true),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'length' => $this->faker->numberBetween(10, 50),
            'width' => $this->faker->numberBetween(10, 50), 
            'height' => $this->faker->numberBetween(5, 20),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'color' => $this->faker->colorName()
        ];
    }

    /**
     * Indicate that the variant belongs to a specific product.
     */
    public function forProduct(Product $product)
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    /**
     * Indicate that the variant is out of stock.
     */
    public function outOfStock()
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the variant is inactive.
     */
    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}