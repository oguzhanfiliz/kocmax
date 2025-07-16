<?php

namespace App\Filament\Resources\SkuConfigurationResource\Pages;

use App\Filament\Resources\SkuConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSkuConfiguration extends EditRecord
{
    protected static string $resource = SkuConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
