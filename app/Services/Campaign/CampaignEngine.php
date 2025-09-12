<?php

declare(strict_types=1);

namespace App\Services\Campaign;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Models\Campaign;
use App\Models\User;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Kampanya motoru: aktif kampanyaları tespit eder, uygun handler'lar ile
 * uygular, öncelik sırasına göre sıralar ve önbellekleme yapar.
 */
class CampaignEngine
{
    /** @var Collection<CampaignHandlerInterface> */
    private Collection $handlers;
    
    private bool $cachingEnabled;
    private int $cacheLifetime;

    /**
     * Yapıcı: önbellek davranışını yapılandırır.
     *
     * @param bool $cachingEnabled Önbellek aktif mi?
     * @param int $cacheLifetime Önbellek yaşam süresi (saniye)
     */
    public function __construct(
        bool $cachingEnabled = true,
        int $cacheLifetime = 3600
    ) {
        $this->handlers = new Collection();
        $this->cachingEnabled = $cachingEnabled;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Campaign handler'ı kaydet
     */
    public function registerHandler(CampaignHandlerInterface $handler): void
    {
        $this->handlers->push($handler);
    }

    /**
     * Belirli bir sepet için uygulanabilir kampanyaları bul ve uygula
     */
    public function applyCampaigns(CartContext $context, ?User $user = null): Collection
    {
        try {
            $cacheKey = $this->getCacheKey($context, $user);
            
            if ($this->cachingEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Aktif kampanyaları getir
            $activeCampaigns = $this->getActiveCampaigns($context);
            
            $results = new Collection();

            // Kampanyaları öncelik sırasına göre sırala
            $sortedCampaigns = $activeCampaigns->sortByDesc(function ($campaign) {
                $handler = $this->handlers->get($campaign->type);
                return $handler ? $handler->getPriority() : 0;
            });

            foreach ($sortedCampaigns as $campaign) {
                $handler = $this->handlers->get($campaign->type);
                
                if (!$handler) {
                    Log::warning("Kampanya handler'ı bulunamadı", [
                        'campaign_id' => $campaign->id,
                        'type' => $campaign->type
                    ]);
                    continue;
                }

                // Kampanyayı uygula
                $result = $handler->apply($campaign, $context, $user);
                
                if ($result->isApplied()) {
                    $results->push([
                        'campaign' => $campaign,
                        'result' => $result,
                        'handler' => $handler
                    ]);

                    // Kampanya kullanım sayısını artır
                    $campaign->incrementUsage();

                    // Birlikte kullanılabilir (stackable) değilse döngüyü durdur
                    if (!$campaign->is_stackable) {
                        break;
                    }
                }
            }

            if ($this->cachingEnabled) {
                Cache::put($cacheKey, $results, $this->cacheLifetime);
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Kampanya motoru hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return new Collection();
        }
    }

    /**
     * Belirli bir kampanyayı kontrol et
     */
    public function checkCampaign(Campaign $campaign, CartContext $context, ?User $user = null): bool
    {
        $handler = $this->handlers->get($campaign->type);
        
        if (!$handler) {
            return false;
        }

        return $handler->canApply($campaign, $context, $user);
    }

    /**
     * Müşteri için mevcut kampanyaları getir
     */
    public function getAvailableCampaigns(string $customerType, ?User $user = null): Collection
    {
        return Campaign::active()
            ->forCustomerType($customerType)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(function ($campaign) use ($user) {
                if ($user && !$campaign->canBeUsedBy($user)) {
                    return false;
                }
                return !$campaign->hasReachedUsageLimit();
            });
    }

    /**
     * Kampanya istatistiklerini getir
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        return [
            'usage_count' => $campaign->usage_count,
            'usage_limit' => $campaign->usage_limit,
            'progress_percentage' => $campaign->getProgressPercentage(),
            'is_active' => $campaign->isActive(),
            'days_remaining' => $campaign->ends_at ? 
                max(0, now()->diffInDays($campaign->ends_at, false)) : null
        ];
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        if ($this->cachingEnabled) {
            if (Cache::supportsTags()) {
                Cache::tags(['campaigns'])->flush();
            } else {
                // Geri dönüş (fallback): belirli kampanya önbellek anahtarlarını temizle
                Cache::forget('campaigns.active');
                Cache::forget('campaigns.rules');
                // Gerekirse ilave belirli anahtarları temizle
            }
        }
    }

    /**
     * Aktif ve bağlamla uyumlu kampanyaları döndürür.
     *
     * @param CartContext $context Sepet bağlamı
     * @return Collection Aktif kampanyalar
     */
    private function getActiveCampaigns(CartContext $context): Collection
    {
        return Campaign::active()
            ->forCustomerType($context->getCustomerType())
            ->whereIn('type', $this->handlers->keys())
            ->get();
    }

    /**
     * Önbellek anahtarı üretir (kullanıcı ve sepet bağlamına göre).
     *
     * @param CartContext $context Sepet bağlamı
     * @param User|null $user Kullanıcı (opsiyonel)
     * @return string Önbellek anahtarı
     */
    private function getCacheKey(CartContext $context, ?User $user = null): string
    {
        $userKey = $user ? $user->id : 'guest';
        $contextHash = md5(serialize([
            'items' => $context->getItems()->toArray(),
            'total' => $context->getTotalAmount(),
            'customer_type' => $context->getCustomerType()
        ]));
        
        return "campaigns.{$userKey}.{$contextHash}";
    }
}