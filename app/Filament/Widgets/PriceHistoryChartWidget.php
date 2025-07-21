<?php

namespace App\Filament\Widgets;

use App\Models\PriceHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PriceHistoryChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Fiyat Değişiklikleri Trendi';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30days';

    protected function getData(): array
    {
        $filter = $this->filter;
        
        $query = PriceHistory::query();
        
        switch ($filter) {
            case '7days':
                $query->where('created_at', '>=', now()->subDays(7));
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'd M';
                break;
            case '30days':
                $query->where('created_at', '>=', now()->subDays(30));
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'd M';
                break;
            case '3months':
                $query->where('created_at', '>=', now()->subMonths(3));
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'd M';
                break;
            case '12months':
                $query->where('created_at', '>=', now()->subYear());
                $groupBy = 'YEAR(created_at), MONTH(created_at)';
                $dateFormat = 'M Y';
                break;
            default:
                $query->where('created_at', '>=', now()->subDays(30));
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'd M';
        }

        $priceIncreases = (clone $query)
            ->selectRaw("$groupBy as date, COUNT(*) as count")
            ->whereRaw('new_price > old_price')
            ->groupByRaw($groupBy)
            ->orderBy('date')
            ->pluck('count', 'date');

        $priceDecreases = (clone $query)
            ->selectRaw("$groupBy as date, COUNT(*) as count")
            ->whereRaw('new_price < old_price')
            ->groupByRaw($groupBy)
            ->orderBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $increasesData = [];
        $decreasesData = [];

        // Get all dates in range
        $startDate = match($filter) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '3months' => now()->subMonths(3),
            '12months' => now()->subYear(),
            default => now()->subDays(30),
        };

        $endDate = now();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            if ($filter === '12months') {
                $key = $currentDate->format('Y-m');
                $label = $currentDate->format('M Y');
                $currentDate->addMonth();
            } else {
                $key = $currentDate->format('Y-m-d');
                $label = $currentDate->format('d M');
                $currentDate->addDay();
            }

            $labels[] = $label;
            $increasesData[] = $priceIncreases->get($key, 0);
            $decreasesData[] = $priceDecreases->get($key, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Fiyat Artışları',
                    'data' => $increasesData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Fiyat Düşüşleri',
                    'data' => $decreasesData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7days' => 'Son 7 Gün',
            '30days' => 'Son 30 Gün',
            '3months' => 'Son 3 Ay',
            '12months' => 'Son 12 Ay',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}