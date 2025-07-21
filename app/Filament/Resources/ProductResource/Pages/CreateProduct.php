<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductAttributeValue;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Kategori verilerini backend'de kontrol et ve gerekli üst kategorileri ekle
        if (isset($data['categories']) && is_array($data['categories'])) {
            $allRequiredCategories = collect();
            
            foreach ($data['categories'] as $categoryId) {
                $category = \App\Models\Category::find($categoryId);
                if (!$category) {
                    continue;
                }
                
                // Kategoriyi ekle
                $allRequiredCategories->push($categoryId);
                
                // Üst kategorileri de ekle
                $parent = $category->parent;
                $visited = [$categoryId];
                $depth = 0;
                
                while ($parent && $depth < 10) {
                    if (in_array($parent->id, $visited)) {
                        break;
                    }
                    $visited[] = $parent->id;
                    $allRequiredCategories->push($parent->id);
                    $parent = $parent->parent;
                    $depth++;
                }
            }
            
            $data['categories'] = $allRequiredCategories->unique()->values()->toArray();
        }
        
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Extract attribute values
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);
        
        return DB::transaction(function () use ($data, $attributeValues) {
            // Create the product
            $product = static::getModel()::create($data);
            
            // Create attribute values
            if (!empty($attributeValues)) {
                foreach ($attributeValues as $attributeId => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    
                    // Handle array values (like from checkbox lists)
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'product_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
            
            // Create default variant
            $product->createDefaultVariant();
            
            return $product;
        });
    }
}
