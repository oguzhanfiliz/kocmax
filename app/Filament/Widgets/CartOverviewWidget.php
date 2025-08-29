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
        try {
            $activeCarts = Cart::where('updated_at', '>=', now()->subHours(24))->count();
            $abandonedCarts = Cart::whereBetween('updated_at', [now()->subWeek(), now()->subHours(24)])->count();
            $totalCartValue = (float) (Cart::where('updated_at', '>=', now()->subDay())->sum('total_amount') ?? 0);
            
            // Cart conversion rate (carts converted to orders in last 30 days)
            $cartsWithOrders = DB::table('carts')
                ->join('orders', 'carts.id', '=', 'orders.cart_id')
                ->where('orders.created_at', '>=', now()->subDays(30))
                ->count();
            
            $totalCartsLast30Days = Cart::where('created_at', '>=', now()->subDays(30))->count();
            $conversionRate = (float) ($totalCartsLast30Days > 0 ? ($cartsWithOrders / $totalCartsLast30Days) * 100 : 0);
        } catch (\Exception $e) {
            // Hata durumunda varsayılan değerler
            $activeCarts = 0;
            $abandonedCarts = 0;
            $totalCartValue = 0.0;
            $conversionRate = 0.0;
        }

        return [
            Stat::make('Aktif Sepetler (24s)', $activeCarts)
                ->description('Son 24 saatte güncellenen sepetler')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart([7, 12, 8, 15, 10, 18, $activeCarts]),

            Stat::make('Terk Edilmiş Sepetler', $abandonedCarts)
                ->description('Son haftada terk edilmiş sepetler')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->chart([15, 12, 18, 10, 8, 15, $abandonedCarts]),

            Stat::make('Toplam Sepet Değeri (24s)', '₺' . number_format($totalCartValue, 2))
                ->description('Aktif sepetlerin toplam değeri')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Dönüşüm Oranı (30g)', number_format($conversionRate, 1) . '%')
                ->description('Sepetten siparişe dönüşüm oranı')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate > 50 ? 'success' : ($conversionRate > 25 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}