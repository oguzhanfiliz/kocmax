<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use Illuminate\Support\Collection;

class B2CPricingStrategy extends AbstractPricingStrategy
{
    public function __construct()
    {
        parent::__construct(CustomerType::B2C, 80); // High priority but lower than B2B
    }

    public function getBasePrice(ProductVariant $variant): Price
    {
        // B2C müşterileri için fiyat TRY'ye çevrilerek hesaplanır
        try {
            $amountTry = $variant->getPriceInCurrency('TRY');
            if ($amountTry <= 0 && $variant->product?->base_price) {
                // Varyant fiyatı yoksa ürün baz fiyatını TRY'ye çevir
                $converter = app(\App\Services\CurrencyConversionService::class);
                $amountTry = $converter->convertPrice(
                    (float) $variant->product->base_price,
                    (string) ($variant->product->base_currency ?? 'TRY'),
                    'TRY'
                );
            }
        } catch (\Throwable $e) {
            // Son çare: ham değerler
            $amountTry = (float) ($variant->price ?? $variant->product->base_price ?? 0);
        }

        return new Price((float) $amountTry, 'TRY');
    }

    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $discounts = collect();

        // Smart Pricing (ProductListResource ile tutarlı indirim yüzdesi)
        try {
            /** @var \App\Services\Pricing\CustomerTypeDetectorService $detector */
            $detector = app(\App\Services\Pricing\CustomerTypeDetectorService::class);
            $smartPercentage = (float) $detector->getDiscountPercentage($customer, $quantity);
            if ($smartPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $smartPercentage,
                        'Smart Pricing',
                        'Kullanıcı tipine göre otomatik indirim',
                        92 // Kampanyadan biraz düşük, B2C için yüksek öncelik
                    )
                );
            }
        } catch (\Throwable $e) {
            // ignore smart pricing if service not available
        }

        // Add customer-specific discounts
        $discounts = $discounts->merge($this->getCustomerDiscounts($variant, $customer, $quantity));

        // Add promotional discounts
        $discounts = $discounts->merge($this->getPromotionalDiscounts($variant, $quantity));

        // Add limited bulk discounts for B2C (smaller quantities)
        $discounts = $discounts->merge($this->getB2CBulkDiscounts($variant, $quantity));

        // Add seasonal/campaign discounts
        $discounts = $discounts->merge($this->getSeasonalDiscounts($variant));

        // Add first-time customer discount
        $discounts = $discounts->merge($this->getFirstTimeCustomerDiscount($variant, $customer));

        return $discounts;
    }

    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Customer loyalty program
        $totalOrders = $customer->orders()->completed()->count();
        
        if ($totalOrders >= 5) {
            $loyaltyPercentage = match(true) {
                $totalOrders >= 50 => 5.0,  // VIP Customer
                $totalOrders >= 25 => 3.0,  // Gold Customer
                $totalOrders >= 10 => 2.0,  // Silver Customer
                $totalOrders >= 5 => 1.0,   // Bronze Customer
                default => 0.0
            };

            if ($loyaltyPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $loyaltyPercentage,
                        'Customer Loyalty Discount',
                        "Thank you for being a loyal customer ({$totalOrders} orders)",
                        70
                    )
                );
            }
        }

        // Birthday discount (if we have customer's birthday)
        if ($customer->birth_date && $customer->birth_date->isCurrentMonth()) {
            $discounts->push(
                Discount::percentage(
                    5.0,
                    'Birthday Special',
                    'Happy Birthday! Enjoy this special discount',
                    80
                )
            );
        }

        return $discounts;
    }

    protected function getPromotionalDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Check for active campaigns
        $campaigns = \App\Models\Campaign::active()
            ->whereHas('products', function($query) use ($variant) {
                $query->where('product_id', $variant->product_id);
            })
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->discount_percentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $campaign->discount_percentage,
                        $campaign->name,
                        $campaign->description ?? 'Special promotional offer',
                        90 // High priority for active campaigns
                    )
                );
            }
        }

        return $discounts;
    }

    protected function getB2CBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Smaller quantity thresholds for B2C customers
        $bulkTiers = [
            ['min_qty' => 3, 'discount' => 2.0, 'name' => 'Buy 3+ Save 2%'],
            ['min_qty' => 5, 'discount' => 5.0, 'name' => 'Buy 5+ Save 5%'],
            ['min_qty' => 10, 'discount' => 8.0, 'name' => 'Buy 10+ Save 8%'],
            ['min_qty' => 20, 'discount' => 12.0, 'name' => 'Buy 20+ Save 12%'],
        ];

        foreach ($bulkTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "Multi-item discount for {$tier['min_qty']}+ items",
                        60
                    )
                );
            }
        }

        // Return only the highest applicable discount
        return $discounts->sortByDesc('value')->take(1);
    }

    protected function getSeasonalDiscounts(ProductVariant $variant): Collection
    {
        $discounts = collect();
        
        $currentMonth = now()->month;
        $currentSeason = $this->getCurrentSeason($currentMonth);

        // Check if product categories match seasonal promotions
        $productCategories = $variant->product->categories->pluck('name')->map(fn($name) => strtolower($name));

        $seasonalPromotions = [
            'winter' => [
                'categories' => ['coat', 'jacket', 'winter', 'thermal'],
                'discount' => 10.0,
                'name' => 'Winter Collection Sale'
            ],
            'summer' => [
                'categories' => ['summer', 't-shirt', 'shorts', 'sandal'],
                'discount' => 15.0,
                'name' => 'Summer Clearance'
            ],
            'spring' => [
                'categories' => ['spring', 'light jacket', 'sweater'],
                'discount' => 12.0,
                'name' => 'Spring Fashion'
            ],
            'autumn' => [
                'categories' => ['autumn', 'fall', 'boot', 'sweater'],
                'discount' => 8.0,
                'name' => 'Autumn Collection'
            ]
        ];

        if (isset($seasonalPromotions[$currentSeason])) {
            $promotion = $seasonalPromotions[$currentSeason];
            
            foreach ($promotion['categories'] as $category) {
                if ($productCategories->contains(fn($cat) => str_contains($cat, $category))) {
                    $discounts->push(
                        Discount::percentage(
                            $promotion['discount'],
                            $promotion['name'],
                            "Seasonal discount for {$currentSeason} collection",
                            40
                        )
                    );
                    break; // Only apply one seasonal discount
                }
            }
        }

        return $discounts;
    }

    protected function getFirstTimeCustomerDiscount(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        if (!$customer) {
            return $discounts;
        }

        // Check if this is customer's first order
        $orderCount = $customer->orders()->count();
        
        if ($orderCount === 0) {
            $discounts->push(
                Discount::percentage(
                    10.0,
                    'First Time Customer',
                    'Welcome! Enjoy 10% off your first order',
                    75 // High priority for first-time customers
                )
            );
        }

        return $discounts;
    }

    private function getCurrentSeason(int $month): string
    {
        return match(true) {
            in_array($month, [12, 1, 2]) => 'winter',
            in_array($month, [3, 4, 5]) => 'spring',
            in_array($month, [6, 7, 8]) => 'summer',
            in_array($month, [9, 10, 11]) => 'autumn',
            default => 'spring'
        };
    }

    public function supports(CustomerType $customerType): bool
    {
        return $customerType->isB2C() && $customerType !== CustomerType::GUEST;
    }
}
