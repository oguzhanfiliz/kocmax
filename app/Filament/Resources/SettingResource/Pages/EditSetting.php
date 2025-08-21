<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Geliştirici Ayarı Düzenle';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Value alanını manually doldur çünkü Setting modelinde hidden
        $setting = $this->getRecord();
        
        // Value alanını görünür yap ve değerini al
        $setting->makeVisible(['value']);
        $data['value'] = $setting->getRawOriginal('value'); // Raw değeri kullan, accessor'u bypass et
        
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
        if ($setting->type === 'boolean' && isset($data['value'])) {
            $data['value'] = filter_var($data['value'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
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
