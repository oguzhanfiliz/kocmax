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
