<?php

namespace App\Filament\Resources\VariantImageResource\Pages;

use App\Filament\Resources\VariantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariantImage extends EditRecord
{
    protected static string $resource = VariantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
