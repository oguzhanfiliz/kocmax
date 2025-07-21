<?php

namespace App\Filament\Resources\PriceHistoryResource\Pages;

use App\Filament\Resources\PriceHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListPriceHistories extends ListRecords
{
    protected static string $resource = PriceHistoryResource::class;

    // No header actions for read-only resource
    protected function getHeaderActions(): array
    {
        return [];
    }
}