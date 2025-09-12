<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use Illuminate\Support\Collection;

class B2BPricingStrategy extends AbstractPricingStrategy
{
    public function __construct()
    {
        parent::__construct(CustomerType::B2B, 100); // Highest priority
    }

    public function getBasePrice(ProductVariant $variant): Price
    {
        // B2B fiyatını TRY'ye çevir ve varsayılan B2B indirimi uygula
        try {
            $amountTry = $variant->getPriceInCurrency('TRY');
            if ($amountTry <= 0 && $variant->product?->base_price) {
                $converter = app(\App\Services\CurrencyConversionService::class);
                $amountTry = $converter->convertPrice(
                    (float) $variant->product->base_price,
                    (string) ($variant->product->base_currency ?? 'TRY'),
                    'TRY'
                );
            }
        } catch (\Throwable $e) {
            $amountTry = (float) ($variant->price ?? $variant->product->base_price ?? 0);
        }

        // Varsayılan B2B indirimi
        $defaultDiscount = $this->customerType->getDefaultDiscountPercentage();
        if ($defaultDiscount > 0) {
            $amountTry = $amountTry * (1 - $defaultDiscount / 100);
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
                        92 // Dealer indirimlerinin altında, yine de yüksek öncelik
                    )
                );
            }
        } catch (\Throwable $e) {
            // ignore smart pricing if service not available
        }

        // Add customer-specific dealer discounts
        $discounts = $discounts->merge($this->getCustomerDiscounts($variant, $customer, $quantity));

        // Add bulk discounts
        $discounts = $discounts->merge($this->getBulkDiscounts($variant, $quantity));

        // Add category-based discounts
        $discounts = $discounts->merge($this->getCategoryDiscounts($variant, $customer));

        // Add volume-based tiered discounts
        $discounts = $discounts->merge($this->getVolumeDiscounts($variant, $quantity));

        // Add loyalty discounts for long-term B2B customers
        $discounts = $discounts->merge($this->getLoyaltyDiscounts($variant, $customer));

        return $discounts;
    }

    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        $discounts = collect();

        if (!$customer || !$customer->hasRole('dealer')) {
            return $discounts;
        }

        // Get specific dealer discounts for this product
        $dealerDiscounts = \App\Models\DealerDiscount::active()
            ->forDealer($customer->id)
            ->forProduct($variant->product_id)
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->get();

        foreach ($dealerDiscounts as $dealerDiscount) {
            $discounts->push(
                $dealerDiscount->discount_type === 'percentage' 
                    ? Discount::percentage(
                        $dealerDiscount->discount_value,
                        'Dealer Discount',
                        "Exclusive dealer pricing for {$customer->name}",
                        95 // Very high priority
                    )
                    : Discount::fixedAmount(
                        $dealerDiscount->discount_value,
                        'Dealer Discount',
                        "Exclusive dealer pricing for {$customer->name}",
                        95
                    )
            );
        }

        return $discounts;
    }

    protected function getVolumeDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Define volume discount tiers for B2B
        $volumeTiers = [
            ['min_qty' => 100, 'discount' => 5.0, 'name' => 'Volume Discount - 100+'],
            ['min_qty' => 500, 'discount' => 10.0, 'name' => 'Volume Discount - 500+'],
            ['min_qty' => 1000, 'discount' => 15.0, 'name' => 'Volume Discount - 1000+'],
            ['min_qty' => 5000, 'discount' => 20.0, 'name' => 'Volume Discount - 5000+'],
        ];

        foreach ($volumeTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "Get {$tier['discount']}% off for {$tier['min_qty']}+ items",
                        85 // High priority but lower than dealer discounts
                    )
                );
            }
        }

        // Return only the highest applicable discount
        return $discounts->sortByDesc('value')->take(1);
    }

    protected function getLoyaltyDiscounts(ProductVariant $variant, ?User $customer = null): Collection
    {
        $discounts = collect();

        if (!$customer || !$customer->hasRole('dealer')) {
            return $discounts;
        }

        // Check customer's order history and relationship duration
        $customerSince = $customer->created_at;
        $monthsAsCustomer = $customerSince->diffInMonths(now());
        
        $totalOrders = $customer->orders()->completed()->count();
        $totalSpent = (float) ($customer->orders()->completed()->sum('total_amount') ?? 0);

        // Loyalty discount based on relationship duration
        if ($monthsAsCustomer >= 12) {
            $loyaltyPercentage = match(true) {
                $monthsAsCustomer >= 60 => 3.0, // 5+ years
                $monthsAsCustomer >= 36 => 2.5, // 3+ years  
                $monthsAsCustomer >= 24 => 2.0, // 2+ years
                $monthsAsCustomer >= 12 => 1.0, // 1+ year
                default => 0.0
            };

            if ($loyaltyPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $loyaltyPercentage,
                        'Loyalty Discount',
                        "Thank you for being a customer for {$monthsAsCustomer} months",
                        50 // Lower priority
                    )
                );
            }
        }

        // High-value customer discount
        if ($totalSpent >= 50000) {
            $vipPercentage = match(true) {
                $totalSpent >= 500000 => 5.0, // VIP
                $totalSpent >= 250000 => 3.0, // Premium
                $totalSpent >= 100000 => 2.0, // Gold
                $totalSpent >= 50000 => 1.0,  // Silver
                default => 0.0
            };

            if ($vipPercentage > 0) {
                $discounts->push(
                    Discount::percentage(
                        $vipPercentage,
                        'VIP Customer Discount',
                        "Exclusive discount for high-value customers",
                        55 // Slightly higher than loyalty
                    )
                );
            }
        }

        return $discounts;
    }

    public function supports(CustomerType $customerType): bool
    {
        return $customerType->isB2B();
    }
}
