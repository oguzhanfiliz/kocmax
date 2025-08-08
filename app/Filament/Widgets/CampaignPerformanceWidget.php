<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\CampaignUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CampaignPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $cacheKey = 'dashboard_campaign_performance_10m';

        [$activeCount, $usage30d, $discountSum30d, $topCampaign] = Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function (): array {
                $activeCount = Campaign::active()->count();

                $since = now()->subDays(30);

                $usage30d = CampaignUsage::where('created_at', '>=', $since)->count();

                $discountSum30d = (float) CampaignUsage::where('created_at', '>=', $since)
                    ->sum('discount_amount');

                $topRow = CampaignUsage::where('created_at', '>=', $since)
                    ->select('campaign_id', DB::raw('COUNT(*) as c'))
                    ->groupBy('campaign_id')
                    ->orderByDesc('c')
                    ->first();

                $topCampaign = null;
                if ($topRow && $topRow->campaign_id) {
                    $topCampaign = Campaign::find($topRow->campaign_id);
                    // Append usage count info in memory
                    if ($topCampaign) {
                        $topCampaign->usage_count_last_30d = (int) ($topRow->c ?? 0);
                    }
                }

                return [
                    (int) $activeCount,
                    (int) $usage30d,
                    (float) $discountSum30d,
                    $topCampaign,
                ];
            }
        );

        $stats = [];

        $stats[] = Stat::make('Aktif Kampanya', $activeCount)
            ->description('Şu anda aktif kampanyalar')
            ->descriptionIcon('heroicon-m-sparkles')
            ->color($activeCount > 0 ? 'success' : 'secondary')
            ->url(route('filament.admin.resources.campaigns.index'));

        $stats[] = Stat::make('30G Kullanım', $usage30d)
            ->description('Son 30 gündeki kampanya kullanımı')
            ->descriptionIcon('heroicon-m-bolt')
            ->color($usage30d > 0 ? 'info' : 'secondary');

        $stats[] = Stat::make('30G İndirim Toplamı', '₺' . number_format($discountSum30d, 2))
            ->description('Kampanyalardan sağlanan indirim')
            ->descriptionIcon('heroicon-m-tag')
            ->color($discountSum30d > 0 ? 'warning' : 'secondary');

        $topLabel = $topCampaign?->name ? ($topCampaign->name . ' (' . ($topCampaign->usage_count_last_30d ?? 0) . ')') : 'Veri yok';
        $stats[] = Stat::make('En Çok Kullanılan (30G)', $topLabel)
            ->description('Son 30 günde en çok kullanılan kampanya')
            ->descriptionIcon('heroicon-m-trophy')
            ->color($topCampaign ? 'success' : 'secondary');

        return $stats;
    }
}


