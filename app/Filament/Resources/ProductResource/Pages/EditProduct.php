<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductAttributeValue;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load attribute values
        $attributeValues = $this->record->attributeValues()->get();
        
        $formattedValues = [];
        foreach ($attributeValues as $value) {
            $formattedValues[$value->product_attribute_id] = $value->value;
        }
        
        $data['attribute_values'] = $formattedValues;
        
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract attribute values
        $attributeValues = $data['attribute_values'] ?? [];
        unset($data['attribute_values']);
        
        // Update the record with regular fields
        $record->update($data);
        
        // Update or create attribute values
        if (!empty($attributeValues)) {
            DB::transaction(function () use ($record, $attributeValues) {
                foreach ($attributeValues as $attributeId => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    
                    // Handle array values (like from checkbox lists)
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    
                    ProductAttributeValue::updateOrCreate(
                        [
                            'product_id' => $record->id,
                            'product_attribute_id' => $attributeId,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            });
        }
        
        return $record;
    }
}