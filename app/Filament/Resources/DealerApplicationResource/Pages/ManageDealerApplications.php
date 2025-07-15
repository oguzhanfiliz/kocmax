<?php

namespace App\Filament\Resources\DealerApplicationResource\Pages;

use App\Filament\Resources\DealerApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDealerApplications extends ManageRecords
{
    protected static string $resource = DealerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
