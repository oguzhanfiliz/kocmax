<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use Filament\Actions;
use App\Filament\Resources\CurrencyResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('currency_logic')
                ->label('Döviz Kuru Mantığı')
                ->icon('heroicon-o-information-circle')
                ->color('warning')
                ->modalContent(view('filament.resources.currency-resource.widgets.currency-info-widget'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat'),
        ];
    }
}
