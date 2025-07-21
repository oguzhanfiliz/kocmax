<?php

namespace App\Filament\Widgets;

use App\Models\CustomerPricingTier;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class CustomerTierDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Müşteri Seviye Dağılımı';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get active customer tiers with user counts
        $tiers = CustomerPricingTier::active()
            ->withCount('users')
            ->get();

        // Get users without tier
        $usersWithoutTier = User::whereNull('pricing_tier_id')
            ->where('is_active', true)
            ->count();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($tiers as $tier) {
            $labels[] = $tier->name;
            $data[] = $tier->users_count;
            $colors[] = $this->getColorForTier($tier->type->value);
        }

        // Add users without tier if any
        if ($usersWithoutTier > 0) {
            $labels[] = 'Seviye Atanmamış';
            $data[] = $usersWithoutTier;
            $colors[] = '#6B7280'; // Gray
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => array_map(fn($color) => $this->darkenColor($color, 0.2), $colors),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    private function getColorForTier(string $type): string
    {
        return match($type) {
            'b2b' => '#3B82F6', // Blue
            'b2c' => '#10B981', // Green
            'wholesale' => '#F59E0B', // Yellow
            'retail' => '#8B5CF6', // Purple
            'guest' => '#6B7280', // Gray
            default => '#64748B', // Slate
        };
    }

    private function darkenColor(string $color, float $factor): string
    {
        // Simple color darkening for border
        $color = ltrim($color, '#');
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        
        $r = max(0, min(255, $r * (1 - $factor)));
        $g = max(0, min(255, $g * (1 - $factor)));
        $b = max(0, min(255, $b * (1 - $factor)));
        
        return sprintf('#%02x%02x%02x', $r, $g, $b);
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
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}