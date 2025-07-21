<?php

namespace App\Filament\Resources\CustomerPricingTierResource\Pages;

use App\Filament\Resources\CustomerPricingTierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerPricingTier extends EditRecord
{
    protected static string $resource = CustomerPricingTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}