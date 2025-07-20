<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductCacheService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('clear_cache')
                ->label('Cache Temizle')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $service = new ProductCacheService();
                    $service->clearAllProductCaches();
                    
                    Notification::make()
                        ->title('Cache başarıyla temizlendi.')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Cache Temizle')
                ->modalDescription('Ürün cache\'ini temizlemek istediğinizden emin misiniz?'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Memory optimized query - sadece gerekli alanları seç
        return static::getResource()::getEloquentQuery()
            ->when(
                request()->has('tableSearch') && !empty(request('tableSearch')),
                fn($query) => $query->where('name', 'like', '%' . request('tableSearch') . '%')
                    ->orWhere('sku', 'like', '%' . request('tableSearch') . '%')
            );
    }
}
