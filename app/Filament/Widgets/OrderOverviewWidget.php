<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrderOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Bugünkü siparişler
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Bu ayki siparişler
        $monthlyOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Bekleyen siparişler
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Bu ayki toplam satış
        $monthlySales = (float) (Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount') ?? 0);

        // İşlenen siparişler (bugün)
        $processedToday = Order::whereDate('updated_at', today())
            ->where('status', 'processing')
            ->count();

        // Teslim edilen siparişler (bu hafta)
        $deliveredThisWeek = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'delivered')
            ->count();

        return [
            Stat::make('Bugünkü Siparişler', $todayOrders)
                ->description('Bugün alınan toplam sipariş')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 12, 8, 15, 9, 6, $todayOrders]),

            Stat::make('Bekleyen Siparişler', $pendingOrders)
                ->description('İşlem bekleyen siparişler')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 10 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.orders.index', ['tableFilters[status][value]' => 'pending'])),

            Stat::make('Bu Ay Toplam', $monthlyOrders)
                ->description('Bu ay alınan toplam sipariş')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Aylık Satış', number_format($monthlySales, 2) . ' ₺')
                ->description('Bu ay ödenen siparişlerin tutarı')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Bugün İşlenen', $processedToday)
                ->description('Bugün işleme alınan siparişler')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info'),

            Stat::make('Bu Hafta Teslim', $deliveredThisWeek)
                ->description('Bu hafta teslim edilen siparişler')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}