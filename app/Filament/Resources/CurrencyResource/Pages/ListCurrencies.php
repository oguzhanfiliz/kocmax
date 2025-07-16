<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use App\Services\ExchangeRateService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function updateExchangeRates(ExchangeRateService $exchangeRateService)
    {
        try {
            $exchangeRateService->updateExchangeRates();
            Notification::make()
                ->title('Exchange rates updated successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Failed to update exchange rates')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
