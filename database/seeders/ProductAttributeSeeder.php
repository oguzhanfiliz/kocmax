<?php

namespace Database\Seeders;

use App\Models\AttributeType;
use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get attribute types
        $selectType = AttributeType::where('name', AttributeType::TYPE_SELECT)->first();
        $colorType = AttributeType::where('name', AttributeType::TYPE_COLOR)->first();
        $textType = AttributeType::where('name', AttributeType::TYPE_TEXT)->first();
        
        if (!$selectType || !$colorType || !$textType) {
            $this->command->error('AttributeTypes not found. Please run AttributeTypeSeeder first.');
            return;
        }
        
        // Get categories
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('Categories not found. Please run CategorySeeder first.');
            return;
        }
        
        // Common attributes for all products
        $this->createAttribute(
            'Renk',
            $colorType->id,
            [
                ['value' => '#000000', 'label' => 'Siyah'],
                ['value' => '#FFFFFF', 'label' => 'Beyaz'],
                ['value' => '#FF0000', 'label' => 'Kırmızı'],
                ['value' => '#0000FF', 'label' => 'Mavi'],
                ['value' => '#FFFF00', 'label' => 'Sarı'],
                ['value' => '#FFA500', 'label' => 'Turuncu'],
                ['value' => '#808080', 'label' => 'Gri'],
                ['value' => '#A52A2A', 'label' => 'Kahverengi'],
                ['value' => '#00FF00', 'label' => 'Yeşil'],
            ],
            true,
            true,
            $categories->pluck('id')->toArray()
        );
        
        // Size attributes for different product types
        $footwearCategory = $categories->where('name', 'Ayak Koruyucular')->first();
        if ($footwearCategory) {
            $this->createAttribute(
                'Numara',
                $selectType->id,
                [
                    ['value' => '36', 'label' => '36'],
                    ['value' => '37', 'label' => '37'],
                    ['value' => '38', 'label' => '38'],
                    ['value' => '39', 'label' => '39'],
                    ['value' => '40', 'label' => '40'],
                    ['value' => '41', 'label' => '41'],
                    ['value' => '42', 'label' => '42'],
                    ['value' => '43', 'label' => '43'],
                    ['value' => '44', 'label' => '44'],
                    ['value' => '45', 'label' => '45'],
                    ['value' => '46', 'label' => '46'],
                ],
                true,
                true,
                [$footwearCategory->id]
            );
            
            $this->createAttribute(
                'Burun Tipi',
                $selectType->id,
                [
                    ['value' => 'celik', 'label' => 'Çelik Burun'],
                    ['value' => 'kompozit', 'label' => 'Kompozit Burun'],
                    ['value' => 'normal', 'label' => 'Normal'],
                ],
                false,
                false,
                [$footwearCategory->id]
            );
        }
        
        $gloveCategory = $categories->where('name', 'El Koruyucular')->first();
        if ($gloveCategory) {
            $this->createAttribute(
                'Beden',
                $selectType->id,
                [
                    ['value' => 'S', 'label' => 'S'],
                    ['value' => 'M', 'label' => 'M'],
                    ['value' => 'L', 'label' => 'L'],
                    ['value' => 'XL', 'label' => 'XL'],
                    ['value' => '2XL', 'label' => '2XL'],
                ],
                true,
                true,
                [$gloveCategory->id]
            );
            
            $this->createAttribute(
                'Malzeme',
                $selectType->id,
                [
                    ['value' => 'nitril', 'label' => 'Nitril'],
                    ['value' => 'lateks', 'label' => 'Lateks'],
                    ['value' => 'deri', 'label' => 'Deri'],
                    ['value' => 'pamuk', 'label' => 'Pamuk'],
                    ['value' => 'polyester', 'label' => 'Polyester'],
                ],
                false,
                false,
                [$gloveCategory->id]
            );
        }
        
        $headCategory = $categories->where('name', 'Kafa Koruyucular')->first();
        if ($headCategory) {
            $this->createAttribute(
                'Beden',
                $selectType->id,
                [
                    ['value' => 'Standart', 'label' => 'Standart'],
                    ['value' => 'S', 'label' => 'S'],
                    ['value' => 'M', 'label' => 'M'],
                    ['value' => 'L', 'label' => 'L'],
                    ['value' => 'XL', 'label' => 'XL'],
                ],
                true,
                true,
                [$headCategory->id]
            );
            
            $this->createAttribute(
                'Ayar Mekanizması',
                $selectType->id,
                [
                    ['value' => 'rachet', 'label' => 'Rachet'],
                    ['value' => 'pinlock', 'label' => 'Pin-Lock'],
                    ['value' => 'kayis', 'label' => 'Kayış'],
                    ['value' => 'yok', 'label' => 'Yok'],
                ],
                false,
                false,
                [$headCategory->id]
            );
        }
        
        $clothingCategory = $categories->where('name', 'Vücut Koruyucular')->first();
        if ($clothingCategory) {
            $this->createAttribute(
                'Beden',
                $selectType->id,
                [
                    ['value' => 'XS', 'label' => 'XS'],
                    ['value' => 'S', 'label' => 'S'],
                    ['value' => 'M', 'label' => 'M'],
                    ['value' => 'L', 'label' => 'L'],
                    ['value' => 'XL', 'label' => 'XL'],
                    ['value' => '2XL', 'label' => '2XL'],
                    ['value' => '3XL', 'label' => '3XL'],
                ],
                true,
                true,
                [$clothingCategory->id]
            );
        }
        
        $eyeCategory = $categories->where('name', 'Göz Koruyucular')->first();
        if ($eyeCategory) {
            $this->createAttribute(
                'Lens Tipi',
                $selectType->id,
                [
                    ['value' => 'seffaf', 'label' => 'Şeffaf'],
                    ['value' => 'fume', 'label' => 'Füme'],
                    ['value' => 'sari', 'label' => 'Sarı'],
                    ['value' => 'aynali', 'label' => 'Aynalı'],
                ],
                true,
                true,
                [$eyeCategory->id]
            );
        }
        
        $this->command->info('Product attributes seeded successfully!');
    }
    
    /**
     * Create a product attribute
     */
    private function createAttribute(
        string $name, 
        int $typeId, 
        array $options, 
        bool $isRequired, 
        bool $isVariant, 
        array $categoryIds
    ): void {
        $attribute = ProductAttribute::updateOrCreate(
            ['name' => $name],
            [
                'slug' => Str::slug($name),
                'attribute_type_id' => $typeId,
                'options' => $options,
                'is_required' => $isRequired,
                'is_variant' => $isVariant,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
        
        // Attach categories
        $attribute->categories()->sync($categoryIds);
    }
} 