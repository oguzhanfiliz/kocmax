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
        
        // Image tipi için ayrı alan
        if ($setting->type === 'image' && $data['value']) {
            $data['image_value'] = [$data['value']];
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
        
        // Image upload için özel işlem
        if ($setting->type === 'image' && isset($data['image_value']) && !empty($data['image_value'])) {
            if (is_array($data['image_value'])) {
                $data['value'] = $data['image_value'][0] ?? $data['value'];
            }
            unset($data['image_value']);
        }
        
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
