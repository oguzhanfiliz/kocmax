<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Contracts\Pricing\PricingStrategyInterface;
use App\Enums\Pricing\CustomerType;
use App\Exceptions\Pricing\InvalidPriceException;
use App\Exceptions\Pricing\PricingException;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Fiyatlandırma motoru: müşteri tipine göre uygun stratejiyi seçer,
 * hesaplamayı gerçekleştirir, önbellekleme ve geri dönüş (fallback)
 * mekanizmalarını yönetir.
 */
class PriceEngine
{
    // Fiyatlandırma motoru: stratejilerle fiyat hesaplar, önbellek ve geri dönüş mekanizması uygular
    /** @var Collection<PricingStrategyInterface> */
    private Collection $strategies;
    
    private CustomerTypeDetector $customerTypeDetector;
    
    private bool $cachingEnabled;
    private int $cacheLifetime;

    /**
     * Yapıcı: bağımlılıkları ve önbellek ayarlarını yapılandırır.
     *
     * @param CustomerTypeDetector $customerTypeDetector Müşteri tipi belirleyicisi
     * @param bool $cachingEnabled Önbellek etkin mi?
     * @param int $cacheLifetime Önbellek yaşam süresi (saniye)
     */
    public function __construct(
        CustomerTypeDetector $customerTypeDetector,
        bool $cachingEnabled = true,
        int $cacheLifetime = 300 // 5 dakika
    ) {
        $this->strategies = collect();
        $this->customerTypeDetector = $customerTypeDetector;
        $this->cachingEnabled = $cachingEnabled;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Strateji ekler ve önceliğe göre (yüksekten düşüğe) sıralar.
     *
     * @param PricingStrategyInterface $strategy Eklenecek strateji
     * @return self
     */
    public function addStrategy(PricingStrategyInterface $strategy): self
    {
        $this->strategies->push($strategy);
        
        // Stratejileri önceliğe göre sırala (en yüksek önce)
        $this->strategies = $this->strategies->sortByDesc(fn($strategy) => $strategy->getPriority());
        
        return $this;
    }

    /**
     * Verilen varyant için fiyat hesaplar; gerekirse önbellek kullanır.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @param User|null $customer Müşteri (opsiyonel)
     * @param array $context Ek bağlam bilgileri
     * @return PriceResult Hesaplanan fiyat sonucu
     * @throws PricingException Hesaplama başarısız olursa fırlatılır
     */
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        try {
            $customerType = $this->customerTypeDetector->detect($customer, $context);
            
            if ($this->cachingEnabled) {
                $cacheKey = $this->generateCacheKey($variant, $quantity, $customer, $context);
                
                return Cache::remember($cacheKey, $this->cacheLifetime, function () use ($variant, $quantity, $customer, $context, $customerType) {
                    return $this->performPriceCalculation($variant, $quantity, $customer, $context, $customerType);
                });
            }
            
            return $this->performPriceCalculation($variant, $quantity, $customer, $context, $customerType);
            
        } catch (\Exception $e) {
            Log::error('Price calculation failed', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new PricingException(
                "Failed to calculate price for variant {$variant->id}: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    /**
     * Fiyat hesaplamayı gerçekleştirir; müşteri tipine göre strateji seçer ve geri dönüş zinciri uygular.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @param User|null $customer Müşteri (opsiyonel)
     * @param array $context Ek bağlam
     * @param CustomerType $customerType Tespit edilen müşteri tipi
     * @return PriceResult Hesaplanan fiyat sonucu
     */
    private function performPriceCalculation(
        ProductVariant $variant,
        int $quantity,
        ?User $customer,
        array $context,
        CustomerType $customerType
    ): PriceResult {
        $preferredStrategy = $this->getStrategyForCustomerType($customerType);
        
        if (!$preferredStrategy) {
            throw new InvalidPriceException("No pricing strategy found for customer type: {$customerType->value}");
        }

        // Müşteri tipine göre geri dönüş (fallback) zinciri oluştur
        $fallbackTypes = [];
        if ($customerType->isB2B()) {
            // Önce B2B uyumlu (WHOLESALE dahil), sonra B2C, en son Guest dene
            $fallbackTypes = [CustomerType::B2B, CustomerType::B2C, CustomerType::GUEST];
        } elseif ($customerType === CustomerType::GUEST) {
            // Önce Guest, sonra yumuşak geri dönüş olarak B2C
            $fallbackTypes = [CustomerType::GUEST, CustomerType::B2C];
        } else {
            // B2C/RETAIL: önce B2C, ardından Guest dene
            $fallbackTypes = [CustomerType::B2C, CustomerType::GUEST];
        }

        // Tiplere göre stratejileri çöz, yinelenenleri kaldır ve null olanları atla
        $candidates = collect($fallbackTypes)
            ->map(fn(CustomerType $type) => $this->getStrategyForCustomerType($type))
            ->filter()
            ->uniqueStrict(fn($s) => get_class($s))
            ->values();

        // Tercih edilen stratejinin listede ilk olduğundan emin ol
        if ($candidates->isEmpty() || get_class($candidates->first()) !== get_class($preferredStrategy)) {
            $candidates->prepend($preferredStrategy);
        }

        // Hesaplama yapabilen ilk stratejiyi bul
        $strategy = $candidates->first(function (PricingStrategyInterface $strategy) use ($variant, $quantity, $customer) {
            try {
                return $strategy->canCalculatePrice($variant, $quantity, $customer);
            } catch (\Throwable $t) {
                return false;
            }
        });

        if (!$strategy) {
            throw new InvalidPriceException("Cannot calculate price for the given parameters");
        }

        $startTime = microtime(true);
        
        $result = $strategy->calculatePrice($variant, $quantity, $customer, $context);
        
        $calculationTime = (microtime(true) - $startTime) * 1000; // Milisaniyeye çevir
        
        // Performans metadatası ekle
        $result = $result->withMetadata('calculation_time_ms', round($calculationTime, 2));
        $result = $result->withMetadata('strategy_used', get_class($strategy));
        if (get_class($strategy) !== get_class($preferredStrategy)) {
            Log::warning('Pricing fallback strategy used', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'detected_customer_type' => $customerType->value,
                'preferred_strategy' => get_class($preferredStrategy),
                'used_strategy' => get_class($strategy)
            ]);
        }
        $result = $result->withMetadata('customer_tier', $this->customerTypeDetector->getCustomerTier($customer));
        
        // Hesaplama çok uzun sürerse performans logu yaz
        if ($calculationTime > 100) { // 100ms eşik
            Log::warning('Slow price calculation detected', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'calculation_time_ms' => $calculationTime,
                'strategy' => get_class($strategy)
            ]);
        }
        
        return $result;
    }

    /**
     * Uygun stratejiye göre mevcut indirimleri döndürür.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     * @param int $quantity Adet
     * @return Collection İndirimler
     */
    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $customerType = $this->customerTypeDetector->detect($customer);
        $strategy = $this->getStrategyForCustomerType($customerType);
        
        if (!$strategy) {
            return collect();
        }
        
        return $strategy->getAvailableDiscounts($variant, $customer, $quantity);
    }

    /**
     * Verilen parametrelerle fiyatın hesaplanabilir olup olmadığını doğrular.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @param User|null $customer Müşteri (opsiyonel)
     * @return bool Hesaplanabiliyorsa true
     */
    public function validatePricing(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        try {
            $customerType = $this->customerTypeDetector->detect($customer);
            $strategy = $this->getStrategyForCustomerType($customerType);
            
            return $strategy?->canCalculatePrice($variant, $quantity, $customer) ?? false;
            
        } catch (\Exception $e) {
            Log::warning('Price validation failed', [
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'customer_id' => $customer?->id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Birden fazla kalem için toplu fiyat hesaplar; hatalı kalemleri atlayarak devam eder.
     *
     * @param array $items Her elemanda `variant` (ProductVariant) ve `quantity` (int) beklenir
     * @param User|null $customer Müşteri (opsiyonel)
     * @param array $context Ek bağlam
     * @return Collection Sonuç listesi
     */
    public function bulkCalculatePrice(array $items, ?User $customer = null, array $context = []): Collection
    {
        $results = collect();
        
        foreach ($items as $item) {
            try {
                $variant = $item['variant'] ?? null;
                $quantity = $item['quantity'] ?? 1;
                
                if (!$variant instanceof ProductVariant) {
                    continue;
                }
                
                $result = $this->calculatePrice($variant, $quantity, $customer, $context);
                $results->push([
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'price_result' => $result
                ]);
                
            } catch (\Exception $e) {
                Log::error('Bulk price calculation item failed', [
                    'item' => $item,
                    'customer_id' => $customer?->id,
                    'error' => $e->getMessage()
                ]);
                
                // Bir öğe başarısız olsa bile diğerleriyle devam et
                continue;
            }
        }
        
        return $results;
    }

    /**
     * Yaygın miktarlar için ön hesaplama yaparak önbelleği ısıtır.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     */
    public function preCalculatePrices(ProductVariant $variant, ?User $customer = null): void
    {
        if (!$this->cachingEnabled) {
            return;
        }
        
        // Yaygın miktarları önceden hesapla
        $commonQuantities = [1, 5, 10, 25, 50, 100];
        
        foreach ($commonQuantities as $quantity) {
            try {
                $this->calculatePrice($variant, $quantity, $customer);
            } catch (\Exception $e) {
                // Ön hesaplama sırasında hataları yok say
                Log::debug('Pre-calculation failed', [
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'customer_id' => $customer?->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Varyant (ve kullanıcı) için fiyat önbelleğini temizler.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param User|null $customer Müşteri (opsiyonel)
     */
    public function clearPriceCache(ProductVariant $variant, ?User $customer = null): void
    {
        if (!$this->cachingEnabled) {
            return;
        }
        
        $pattern = "price_calculation:{$variant->id}:*";
        if ($customer) {
            $pattern = "price_calculation:{$variant->id}:{$customer->id}:*";
        }
        
        // Desene uyan önbellek kayıtlarını temizle
        $keys = Cache::getStore()->getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getStore()->getRedis()->del(...$keys);
        }
    }

    /**
     * Verilen müşteri tipini destekleyen ilk stratejiyi döndürür.
     *
     * @param CustomerType $customerType Müşteri tipi
     * @return PricingStrategyInterface|null Uygun strateji veya null
     */
    private function getStrategyForCustomerType(CustomerType $customerType): ?PricingStrategyInterface
    {
        return $this->strategies->first(fn(PricingStrategyInterface $strategy) => $strategy->supports($customerType));
    }

    /**
     * Fiyat hesaplaması için deterministik önbellek anahtarı üretir.
     *
     * @param ProductVariant $variant Ürün varyantı
     * @param int $quantity Adet
     * @param User|null $customer Müşteri (opsiyonel)
     * @param array $context Ek bağlam
     * @return string Önbellek anahtarı
     */
    private function generateCacheKey(ProductVariant $variant, int $quantity, ?User $customer, array $context): string
    {
        $customerKey = $customer ? $customer->id : 'guest';
        $contextHash = md5(serialize($context));

        $taxSignature = $this->generateTaxSignature($variant);
        $defaultTaxSignature = $this->getDefaultTaxSignature();

        return "price_calculation:{$variant->id}:{$customerKey}:{$quantity}:{$contextHash}:{$taxSignature}:{$defaultTaxSignature}";
    }

    private function generateTaxSignature(ProductVariant $variant): string
    {
        $product = $variant->product;
        $productTax = $product?->tax_rate;

        $categoryIds = [];
        if ($product) {
            $categoryIds = $product->categories->pluck('id')->all();
            $cachedKey = sprintf('product_categories_tax:%s', $product->id);
            $categoryIds = cache()->remember($cachedKey, 300, fn() => $product->categories()->pluck('categories.id')->all());
        }

        $parts = [
            'product:' . ($productTax !== null ? round((float) $productTax, 4) : 'null'),
        ];

        if (!empty($categoryIds)) {
            $categoryRates = 
                \App\Models\Category::whereIn('id', $categoryIds)
                    ->pluck('tax_rate', 'id')
                    ->map(fn($rate) => $rate !== null ? round((float) $rate, 4) : null)
                    ->toArray();

            ksort($categoryRates);

            foreach ($categoryRates as $categoryId => $rate) {
                $parts[] = "category:{$categoryId}:" . ($rate !== null ? $rate : 'null');
            }
        }

        return sha1(implode('|', $parts));
    }

    private function getDefaultTaxSignature(): string
    {
        $defaultTax = \App\Helpers\SettingHelper::defaultTaxRate();

        return 'default:' . round((float) $defaultTax, 4);
    }

    /**
     * Kayıtlı stratejilerin koleksiyonunu döndürür.
     *
     * @return Collection<PricingStrategyInterface>
     */
    public function getRegisteredStrategies(): Collection
    {
        return $this->strategies;
    }

    /**
     * Belirtilen müşteri tipi için strateji mevcut mu kontrol eder.
     *
     * @param CustomerType $customerType Müşteri tipi
     * @return bool Mevcutsa true
     */
    public function hasStrategyFor(CustomerType $customerType): bool
    {
        return $this->getStrategyForCustomerType($customerType) !== null;
    }

    /**
     * Önbelleği etkinleştirir ve yaşam süresini ayarlar.
     *
     * @param int $lifetime Önbellek yaşam süresi (saniye)
     * @return self
     */
    public function enableCaching(int $lifetime = 300): self
    {
        $this->cachingEnabled = true;
        $this->cacheLifetime = $lifetime;
        
        return $this;
    }

    /**
     * Önbelleği devre dışı bırakır.
     *
     * @return self
     */
    public function disableCaching(): self
    {
        $this->cachingEnabled = false;
        
        return $this;
    }
}
