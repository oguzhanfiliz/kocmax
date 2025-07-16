<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Services\ExchangeRateService;
use Filament\Notifications\Notification;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Döviz Kuru Mantığı')
                ->icon('heroicon-o-information-circle')
                ->modalContent(view('filament.resources.currency-resource.widgets.currency-info-widget'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat'),
            Actions\CreateAction::make(),
            Actions\Action::make('updateExchangeRates')
                ->label('Döviz Kurlarını Güncelle')
                ->icon('heroicon-o-arrow-path')
                ->action(function (ExchangeRateService $exchangeRateService) {
                    try {
                        $exchangeRateService->updateExchangeRates();
                        Notification::make()
                            ->title('Döviz kurları başarıyla güncellendi.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Döviz kurları güncellenirken bir hata oluştu.')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
