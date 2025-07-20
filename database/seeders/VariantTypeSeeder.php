<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VariantType;
use App\Models\VariantOption;

class VariantTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Renk Varyant Türü
        $colorType = VariantType::create([
            'name' => 'Color',
            'slug' => 'color',
            'display_name' => 'Renk',
            'input_type' => 'color',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Renk Seçenekleri
        $colors = [
            ['name' => 'Black', 'value' => 'Siyah', 'hex_color' => '#000000'],
            ['name' => 'White', 'value' => 'Beyaz', 'hex_color' => '#FFFFFF'],
            ['name' => 'Red', 'value' => 'Kırmızı', 'hex_color' => '#FF0000'],
            ['name' => 'Blue', 'value' => 'Mavi', 'hex_color' => '#0000FF'],
            ['name' => 'Green', 'value' => 'Yeşil', 'hex_color' => '#00FF00'],
            ['name' => 'Yellow', 'value' => 'Sarı', 'hex_color' => '#FFFF00'],
            ['name' => 'Orange', 'value' => 'Turuncu', 'hex_color' => '#FFA500'],
            ['name' => 'Gray', 'value' => 'Gri', 'hex_color' => '#808080'],
            ['name' => 'Navy', 'value' => 'Lacivert', 'hex_color' => '#000080'],
        ];

        foreach ($colors as $index => $color) {
            VariantOption::create([
                'variant_type_id' => $colorType->id,
                'name' => $color['name'],
                'value' => $color['value'],
                'slug' => strtolower($color['name']),
                'hex_color' => $color['hex_color'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        // Beden Varyant Türü
        $sizeType = VariantType::create([
            'name' => 'Size',
            'slug' => 'size',
            'display_name' => 'Beden',
            'input_type' => 'select',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Beden Seçenekleri
        $sizes = [
            ['name' => 'XS', 'value' => 'XS'],
            ['name' => 'S', 'value' => 'S'],
            ['name' => 'M', 'value' => 'M'],
            ['name' => 'L', 'value' => 'L'],
            ['name' => 'XL', 'value' => 'XL'],
            ['name' => '2XL', 'value' => '2XL'],
            ['name' => '3XL', 'value' => '3XL'],
            ['name' => '4XL', 'value' => '4XL'],
        ];

        foreach ($sizes as $index => $size) {
            VariantOption::create([
                'variant_type_id' => $sizeType->id,
                'name' => $size['name'],
                'value' => $size['value'],
                'slug' => strtolower(str_replace(' ', '-', $size['name'])),
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        // Ayakkabı Numarası Varyant Türü
        $shoeType = VariantType::create([
            'name' => 'Shoe Size',
            'slug' => 'shoe-size',
            'display_name' => 'Ayakkabı Numarası',
            'input_type' => 'select',
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Ayakkabı Numarası Seçenekleri
        for ($i = 36; $i <= 48; $i++) {
            VariantOption::create([
                'variant_type_id' => $shoeType->id,
                'name' => (string)$i,
                'value' => (string)$i,
                'slug' => (string)$i,
                'sort_order' => $i - 36,
                'is_active' => true,
            ]);
        }
    }
}
