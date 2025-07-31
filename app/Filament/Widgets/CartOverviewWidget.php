<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Cart;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CartOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeCarts = Cart::where('updated_at', '>=', now()->subHours(24))->count();
        $abandonedCarts = Cart::whereBetween('updated_at', [now()->subWeek(), now()->subHours(24)])->count();
        $totalCartValue = Cart::where('updated_at', '>=', now()->subDay())->sum('total_amount');
        
        // Cart conversion rate (carts converted to orders in last 30 days)
        $cartsWithOrders = DB::table('carts')
            ->join('orders', 'carts.id', '=', 'orders.cart_id')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->count();
        
        $totalCartsLast30Days = Cart::where('created_at', '>=', now()->subDays(30))->count();
        $conversionRate = $totalCartsLast30Days > 0 ? ($cartsWithOrders / $totalCartsLast30Days) * 100 : 0;

        return [
            Stat::make('Active Carts (24h)', $activeCarts)
                ->description('Carts updated in last 24 hours')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart([7, 12, 8, 15, 10, 18, $activeCarts]),

            Stat::make('Abandoned Carts', $abandonedCarts)
                ->description('Carts abandoned in last week')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart([15, 12, 18, 10, 8, 15, $abandonedCarts]),

            Stat::make('Total Cart Value (24h)', '₺' . number_format($totalCartValue, 2))
                ->description('Total value of active carts')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Conversion Rate (30d)', number_format($conversionRate, 1) . '%')
                ->description('Cart to order conversion rate')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate > 50 ? 'success' : ($conversionRate > 25 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}