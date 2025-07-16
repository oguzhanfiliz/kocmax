<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Önce bir isim oluşturulur.
        $name = $this->faker->unique()->words(3, true);

        return [
            // Modelin diğer alanları için sahte veri tanımlamaları.
            'name' => $name,
            'slug' => function (array $attributes) {
                // 'slug' her zaman 'name' attribute'undan oluşturulur.
                // Bu, factory create içinde name override edilse bile doğru slug'ı garantiler.
                return Str::slug($attributes['name']);
            },
            'description' => $this->faker->paragraph,
            'parent_id' => null, // Varsayılan olarak ana kategori
            'is_active' => $this->faker->boolean(90), // %90 ihtimalle aktif
        ];
    }
}
