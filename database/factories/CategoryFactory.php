<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'İş Kıyafetleri', 'İş Ayakkabıları', 'İş Eldivenleri', 'Kafa Koruyucular',
            'Göz Koruyucular', 'Solunum Koruyucular', 'Yüksekte Çalışma Ekipmanları',
            'İlk Yardım ve Sağlık', 'Trafik ve Yol Güvenliği', 'Gaz Dedektörleri'
        ]) . ' ' . $this->faker->unique()->word;

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph,
            'is_active' => true,
        ];
    }
}
