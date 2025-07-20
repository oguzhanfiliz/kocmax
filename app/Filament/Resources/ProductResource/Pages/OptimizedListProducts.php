<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Services\ProductCacheService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class OptimizedListProducts extends ListRecords
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
                    
                    $this->notify('success', 'Cache başarıyla temizlendi.');
                })
                ->requiresConfirmation()
                ->modalHeading('Cache Temizle')
                ->modalDescription('Ürün cache\'ini temizlemek istediğinizden emin misiniz?'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Optimize query for memory efficiency
        return parent::getTableQuery()
            ->select([
                'products.id',
                'products.name',
                'products.slug',
                'products.sku',
                'products.base_price',
                'products.brand',
                'products.brand_id',
                'products.is_active',
                'products.is_featured',
                'products.created_at',
                'products.sort_order'
            ])
            ->with(['brand:id,name'])
            ->withCount(['variants']);
    }
}