<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LowStockOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $criticalCount = ProductVariant::query()
            ->active()
            ->lowStock()
            ->count();

        $outOfStock = ProductVariant::query()
            ->active()
            ->where('stock', '<=', 0)
            ->count();

        $nearLowStock = ProductVariant::query()
            ->active()
            ->whereColumn('stock', '<=', 'min_stock_level + 3')
            ->whereColumn('stock', '>', 'min_stock_level')
            ->count();

        return [
            Stat::make('Kritik Stokta Varyant', $criticalCount)
                ->description('Min stok seviyesinin altında veya eşit')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticalCount > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.products.index', [
                    'tableFilters[variants_low_stock][isActive]' => true,
                ])),

            Stat::make('Tükendi', $outOfStock)
                ->description('Stok adedi 0 veya altı')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color($outOfStock > 0 ? 'danger' : 'success'),

            Stat::make('Yakın Kritik', $nearLowStock)
                ->description('Min stok seviyesine yakın')
                ->descriptionIcon('heroicon-m-warning')
                ->color($nearLowStock > 0 ? 'warning' : 'success'),
        ];
    }
}


