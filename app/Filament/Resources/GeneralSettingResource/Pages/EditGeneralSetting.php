<?php

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use App\Filament\Resources\GeneralSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralSetting extends EditRecord
{
    protected static string $resource = GeneralSettingResource::class;

    protected function getHeaderActions(): array
    {
        $setting = $this->getRecord();
        
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->visible(fn () => !$setting->isEssential())
                ->modalDescription(fn () => $setting->isEssential() 
                    ? 'Bu ayar sistem için gereklidir ve silinemez.' 
                    : 'Bu ayarı silmek istediğinizden emin misiniz?'
                ),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Genel Ayar Düzenle';
    }
    

    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $setting = $this->getRecord();
        $setting->makeVisible(['value']);
        
        $rawValue = $setting->getRawOriginal('value');
        $data['value'] = $rawValue;
        
        // Boolean tipi için ayrı alan
        if ($setting->type === 'boolean') {
            $data['boolean_value'] = in_array($rawValue, ['1', 'true', 'on', 'yes'], true);
        }
        
        // Array tipi için ayrı alan (footer menu items vb.)
        if ($setting->type === 'array' && $rawValue) {
            // JSON string'i array'e çevir
            $arrayData = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;

            if (in_array($setting->key, ['footer_account_items', 'footer_info_items'])) {
                $data['array_value'] = $arrayData ?: [];
            } else {
                $data['key_value_array'] = $arrayData ?: [];
            }
        }
        
        // Image tipi için ayrı alan
        if ($setting->type === 'image' && $rawValue) {
            // FileUpload component'i için tam storage path vermen gerekiyor
            $imagePath = $rawValue;
            
            // Eğer sadece dosya adı ise, settings/images/ dizinini ekle
            if (!str_contains($imagePath, '/') && !str_starts_with($imagePath, 'settings/')) {
                $imagePath = 'settings/images/' . $imagePath;
            }
            
            
            $data['image_value'] = [$imagePath];
        }
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $setting = $this->getRecord();
        
        
        // Boolean değerler için özel işlem
        if ($setting->type === 'boolean' && isset($data['boolean_value'])) {
            $data['value'] = $data['boolean_value'] ? '1' : '0';
            unset($data['boolean_value']); // Form field'ını kaldır
        }
        
        // Array değerler için özel işlem
        if ($setting->type === 'array') {
            if (isset($data['array_value'])) {
                $data['value'] = json_encode($data['array_value']);
                unset($data['array_value']);
            }
            if (isset($data['key_value_array'])) {
                $data['value'] = json_encode($data['key_value_array']);
                unset($data['key_value_array']);
            }
        }
        
        // Image upload için özel işlem
        if ($setting->type === 'image' && isset($data['image_value']) && !empty($data['image_value'])) {
            // FileUpload component hem string hem array olarak gelebilir
            if (is_array($data['image_value'])) {
                $data['value'] = $data['image_value'][0] ?? $data['value'];
            } else {
                $data['value'] = $data['image_value'];
            }
            
            
            unset($data['image_value']);
        }
        
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
