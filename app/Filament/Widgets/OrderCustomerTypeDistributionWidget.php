<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class OrderCustomerTypeDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'B2B vs B2C Sipariş Dağılımı';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30days';

    protected function getType(): string
    {
        return 'doughnut';
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

        $startDate = match ($filter) {
            '7days' => now()->startOfDay()->subDays(6),
            '12months' => now()->startOfMonth()->subMonths(11),
            default => now()->startOfDay()->subDays(29),
        };

        $cacheKey = sprintf('dashboard_order_customer_type_dist_%s', $filter);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($startDate) {
            $base = Order::query()
                ->where('payment_status', 'paid')
                ->where('created_at', '>=', $startDate);

            $b2b = (clone $base)->where('customer_type', 'B2B')->count();
            $b2c = (clone $base)->where('customer_type', 'B2C')->count();

            $others = (clone $base)
                ->whereNotIn('customer_type', ['B2B', 'B2C'])
                ->count();

            $labels = ['B2B', 'B2C'];
            $data = [$b2b, $b2c];
            $colors = ['#3B82F6', '#10B981'];

            if ($others > 0) {
                $labels[] = 'Diğer';
                $data[] = $others;
                $colors[] = '#6B7280';
            }

            return [
                'datasets' => [
                    [
                        'data' => $data,
                        'backgroundColor' => $colors,
                        'borderColor' => array_map(fn($c) => self::darkenColor($c, 0.2), $colors),
                        'borderWidth' => 2,
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
        ];
    }

    private static function darkenColor(string $hexColor, float $factor): string
    {
        $hex = ltrim($hexColor, '#');
        $r = max(0, min(255, (int) round(hexdec(substr($hex, 0, 2)) * (1 - $factor))));
        $g = max(0, min(255, (int) round(hexdec(substr($hex, 2, 2)) * (1 - $factor))));
        $b = max(0, min(255, (int) round(hexdec(substr($hex, 4, 2)) * (1 - $factor))));
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}


