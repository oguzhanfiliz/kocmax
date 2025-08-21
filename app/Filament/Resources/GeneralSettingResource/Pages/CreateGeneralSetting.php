<?php

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use App\Filament\Resources\GeneralSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGeneralSetting extends CreateRecord
{
    protected static string $resource = GeneralSettingResource::class;
    
    public function getTitle(): string
    {
        return 'Yeni Genel Ayar';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        
        // Boolean değerler için özel işlem
        if (isset($data['type']) && $data['type'] === 'boolean' && isset($data['value'])) {
            $data['value'] = filter_var($data['value'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        }
        
        // Auto-generate key from label if not provided or empty
        if (empty($data['key']) && !empty($data['label'])) {
            $data['key'] = \Str::snake(strtolower($data['label']));
        }
        
        return $data;
    }
}