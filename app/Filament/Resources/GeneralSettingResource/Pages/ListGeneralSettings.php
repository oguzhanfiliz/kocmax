<?php

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use App\Filament\Resources\GeneralSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralSettings extends ListRecords
{
    protected static string $resource = GeneralSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Ayar')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Genel Ayarlar';
    }
    
    public function getHeading(): string
    {
        return 'Site ve iletişim ayarlarınızı buradan yönetebilirsiniz';
    }
}