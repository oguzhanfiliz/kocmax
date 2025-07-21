<?php

namespace App\Filament\Resources\CustomerPricingTierResource\Pages;

use App\Filament\Resources\CustomerPricingTierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerPricingTiers extends ListRecords
{
    protected static string $resource = CustomerPricingTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}