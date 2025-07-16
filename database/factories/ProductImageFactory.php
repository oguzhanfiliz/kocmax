<?php

namespace Database\Factories;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'image_path' => 'https://via.placeholder.com/800x600.png/0077be/ffffff?text=ISG+Urunu',
            'alt_text' => $this->faker->sentence(3),
            'is_primary' => $this->faker->boolean(20),
        ];
    }
}
