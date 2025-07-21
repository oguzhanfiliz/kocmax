<?php

namespace App\Filament\Widgets;

use App\Models\CustomerPricingTier;
use App\Models\PriceHistory;
use App\Models\PricingRule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PricingOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Aktif Fiyatlandırma Kuralları', $this->getActivePricingRules())
                ->description('Şu anda aktif olan kurallar')
                ->descriptionIcon('heroicon-m-scale')
                ->color('success'),
                
            Stat::make('Müşteri Seviyeleri', $this->getCustomerTiers())
                ->description('Tanımlı fiyatlandırma seviyeleri')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
                
            Stat::make('Bu Ay Fiyat Değişiklikleri', $this->getMonthlyPriceChanges())
                ->description($this->getPriceChangesTrend())
                ->descriptionIcon($this->getPriceChangesIcon())
                ->color($this->getPriceChangesColor()),
                
            Stat::make('Ortalama İndirim Oranı', $this->getAverageDiscountRate() . '%')
                ->description('Aktif kurallar ortalaması')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),
        ];
    }

    private function getActivePricingRules(): int
    {
        return PricingRule::active()->count();
    }

    private function getCustomerTiers(): int
    {
        return CustomerPricingTier::active()->count();
    }

    private function getMonthlyPriceChanges(): int
    {
        return PriceHistory::where('created_at', '>=', now()->startOfMonth())->count();
    }

    private function getPriceChangesTrend(): string
    {
        $currentMonth = $this->getMonthlyPriceChanges();
        $lastMonth = PriceHistory::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->count();

        if ($lastMonth === 0) {
            return 'Geçen ay veri yok';
        }

        $change = (($currentMonth - $lastMonth) / $lastMonth) * 100;
        $trend = $change > 0 ? 'artış' : ($change < 0 ? 'azalış' : 'değişim yok');
        
        return sprintf('Geçen aya göre %%%.1f %s', abs($change), $trend);
    }

    private function getPriceChangesIcon(): string
    {
        $currentMonth = $this->getMonthlyPriceChanges();
        $lastMonth = PriceHistory::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->count();

        if ($currentMonth > $lastMonth) {
            return 'heroicon-m-arrow-trending-up';
        } elseif ($currentMonth < $lastMonth) {
            return 'heroicon-m-arrow-trending-down';
        }

        return 'heroicon-m-minus';
    }

    private function getPriceChangesColor(): string
    {
        $currentMonth = $this->getMonthlyPriceChanges();
        $lastMonth = PriceHistory::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->count();

        if ($currentMonth > $lastMonth) {
            return 'warning';
        } elseif ($currentMonth < $lastMonth) {
            return 'success';
        }

        return 'secondary';
    }

    private function getAverageDiscountRate(): float
    {
        $activeTiers = CustomerPricingTier::active()->get();
        
        if ($activeTiers->isEmpty()) {
            return 0;
        }

        return round($activeTiers->avg('discount_percentage'), 1);
    }
}