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
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }
}