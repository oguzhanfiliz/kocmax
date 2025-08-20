<?php

declare(strict_types=1);

namespace App\Filament\Resources\SiteFeatureResource\Pages;

use App\Filament\Resources\SiteFeatureResource;
use Filament\Resources\Pages\ListRecords;

class ListSiteFeatures extends ListRecords
{
    protected static string $resource = SiteFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - managed through individual page
        ];
    }
}