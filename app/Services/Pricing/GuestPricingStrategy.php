<?php

declare(strict_types=1);

namespace App\Services\Pricing;

use App\Enums\Pricing\CustomerType;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Pricing\Discount;
use App\ValueObjects\Pricing\Price;
use Illuminate\Support\Collection;

class GuestPricingStrategy extends AbstractPricingStrategy
{
    public function __construct()
    {
        parent::__construct(CustomerType::GUEST, 60); // Lower priority
    }

    public function getBasePrice(ProductVariant $variant): Price
    {
        // Misafir kullanıcılar için fiyat TRY'ye çevrilerek hesaplanır
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

        return new Price((float) $amountTry, 'TRY');
    }

    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null,
        int $quantity = 1
    ): Collection {
        $discounts = collect();

        // Limited discounts for guest users
        $discounts = $discounts->merge($this->getPublicPromotions($variant, $quantity));
        $discounts = $discounts->merge($this->getMinimumBulkDiscounts($variant, $quantity));
        $discounts = $discounts->merge($this->getSignUpIncentiveDiscounts($variant));

        return $discounts;
    }

    protected function getCustomerDiscounts(ProductVariant $variant, ?User $customer = null, int $quantity = 1): Collection
    {
        // Guests don't have customer-specific discounts
        return collect();
    }

    protected function getPublicPromotions(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Only public campaigns that don't require login
        $campaigns = \App\Models\Campaign::active()
            ->where('is_public', true) // Assuming there's a public flag
            ->whereHas('products', function($query) use ($variant) {
                $query->where('product_id', $variant->product_id);
            })
            ->get();

        foreach ($campaigns as $campaign) {
            if ($campaign->discount_percentage > 0 && $campaign->discount_percentage <= 15) { // Limit discount for guests
                $discounts->push(
                    Discount::percentage(
                        $campaign->discount_percentage,
                        $campaign->name,
                        $campaign->description ?? 'Limited time offer',
                        70 // Good priority for public campaigns
                    )
                );
            }
        }

        return $discounts;
    }

    protected function getMinimumBulkDiscounts(ProductVariant $variant, int $quantity): Collection
    {
        $discounts = collect();

        // Very limited bulk discounts for guests
        $bulkTiers = [
            ['min_qty' => 5, 'discount' => 2.0, 'name' => 'Multi-item Discount'],
            ['min_qty' => 10, 'discount' => 5.0, 'name' => 'Bulk Purchase Discount'],
        ];

        foreach ($bulkTiers as $tier) {
            if ($quantity >= $tier['min_qty']) {
                $discounts->push(
                    Discount::percentage(
                        $tier['discount'],
                        $tier['name'],
                        "Save when you buy {$tier['min_qty']}+ items",
                        50
                    )
                );
            }
        }

        // Return only the highest applicable discount
        return $discounts->sortByDesc('value')->take(1);
    }

    protected function getSignUpIncentiveDiscounts(ProductVariant $variant): Collection
    {
        $discounts = collect();

        // Encourage guest users to sign up
        $discounts->push(
            Discount::percentage(
                5.0,
                'Sign Up & Save',
                'Create an account to unlock this discount and more benefits',
                30 // Lower priority as it's not immediately applicable
            )
        );

        return $discounts;
    }

    public function canCalculatePrice(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool {
        // Guests can see prices but with limitations
        try {
            $this->validateInputs($variant, $quantity);
            
            // Guests might have quantity limitations
            if ($quantity > 100) { // Arbitrary limit for guests
                return false;
            }
            
            return $this->getBasePrice($variant)->getAmount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function supports(CustomerType $customerType): bool
    {
        return $customerType === CustomerType::GUEST;
    }
}
