<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SalesTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ciro ve Sipariş Trendi';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30days';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7days' => 'Son 7 Gün',
            '30days' => 'Son 30 Gün',
            '12months' => 'Son 12 Ay',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? '30days';

        [$startDate, $groupBy, $labelFormat, $step] = match ($filter) {
            '7days' => [now()->startOfDay()->subDays(6), 'DATE(created_at)', 'd M', 'day'],
            '12months' => [now()->startOfMonth()->subMonths(11), 'YEAR(created_at), MONTH(created_at)', 'M Y', 'month'],
            default => [now()->startOfDay()->subDays(29), 'DATE(created_at)', 'd M', 'day'],
        };

        $cacheKey = sprintf('dashboard_sales_trend_%s', $filter);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($startDate, $groupBy, $labelFormat, $step) {
            $baseQuery = Order::query()
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', $startDate);

            $counts = (clone $baseQuery)
                ->selectRaw("$groupBy as g, COUNT(*) as c")
                ->groupByRaw($groupBy)
                ->orderBy('g')
                ->pluck('c', 'g');

            $revenues = (clone $baseQuery)
                ->selectRaw("$groupBy as g, SUM(total_amount) as s")
                ->groupByRaw($groupBy)
                ->orderBy('g')
                ->pluck('s', 'g');

            $labels = [];
            $orderCounts = [];
            $orderRevenues = [];

            $current = Carbon::parse($startDate)->copy();
            $end = now();

            while ($current <= $end) {
                if ($step === 'month') {
                    $key = $current->format('Y-m');
                    $label = $current->format($labelFormat);
                    $current->addMonth();
                } else {
                    $key = $current->format('Y-m-d');
                    $label = $current->format($labelFormat);
                    $current->addDay();
                }

                $labels[] = $label;
                $orderCounts[] = (int) ($counts[$key] ?? 0);
                $orderRevenues[] = (float) ($revenues[$key] ?? 0.0);
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Sipariş Adedi',
                        'data' => $orderCounts,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                        'tension' => 0.3,
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => 'Ciro (₺)',
                        'data' => $orderRevenues,
                        'borderColor' => 'rgb(34, 197, 94)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                        'tension' => 0.3,
                        'yAxisID' => 'y1',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => ['display' => true, 'text' => 'Sipariş'],
                ],
                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'beginAtZero' => true,
                    'grid' => ['drawOnChartArea' => false],
                    'title' => ['display' => true, 'text' => 'Ciro (₺)'],
                ],
            ],
        ];
    }
}


