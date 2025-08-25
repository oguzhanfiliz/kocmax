<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\VariantImage;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VariantImage>
 */
class VariantImageFactory extends Factory
{
    protected $model = VariantImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Sample image URLs for different product types
        $sampleImages = [
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=600&fit=crop',
        ];

        return [
            'product_variant_id' => ProductVariant::factory(),
            'image_url' => $this->faker->randomElement($sampleImages),
            'alt_text' => $this->faker->sentence(3),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_primary' => false,
        ];
    }

    /**
     * Indicate that the image belongs to a specific variant.
     */
    public function forVariant(ProductVariant $variant)
    {
        return $this->state(fn (array $attributes) => [
            'product_variant_id' => $variant->id,
        ]);
    }

    /**
     * Indicate that this is a primary image.
     */
    public function primary()
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
            'sort_order' => 0,
        ]);
    }

    /**
     * Create a specific color variant image.
     */
    public function colorVariant(string $color = null)
    {
        $colorImages = [
            'siyah' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&h=600&fit=crop',
            'beyaz' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&h=600&fit=crop',
            'mavi' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&h=600&fit=crop',
            'kırmızı' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&h=600&fit=crop',
        ];

        $imageUrl = $color && isset($colorImages[strtolower($color)]) 
            ? $colorImages[strtolower($color)]
            : $this->faker->randomElement($colorImages);

        return $this->state(fn (array $attributes) => [
            'image_url' => $imageUrl,
            'alt_text' => ($color ?? 'Renkli') . ' varyant görseli',
        ]);
    }
}