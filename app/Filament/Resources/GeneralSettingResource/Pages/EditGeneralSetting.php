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
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Genel Ayar Düzenle';
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
        $data['value'] = $setting->value; // Accessor'dan değeri al
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}