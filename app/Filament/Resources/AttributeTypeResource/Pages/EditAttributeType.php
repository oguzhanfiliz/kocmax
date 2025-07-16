<?php

namespace App\Filament\Resources\AttributeTypeResource\Pages;

use App\Filament\Resources\AttributeTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttributeType extends EditRecord
{
    protected static string $resource = AttributeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
