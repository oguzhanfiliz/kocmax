<?php

declare(strict_types=1);

namespace Tests\Unit\Cart;

use App\ValueObjects\Cart\CartSummary;
use Tests\TestCase;

class CartSummaryTest extends TestCase
{
    public function test_applied_discounts_are_deduplicated_by_type(): void
    {
        $summary = new CartSummary(
            subtotal: 100.0,
            discount: 25.0,
            total: 75.0,
            itemCount: 3,
            itemDetails: [],
            appliedDiscounts: [
                ['type' => 'pricing_service', 'description' => 'Automatic discount', 'amount' => 10.0],
                ['type' => 'Pricing_Service', 'description' => 'Automatic discount', 'amount' => 5.0],
                ['type' => 'coupon', 'code' => 'SAVE10', 'amount' => 10.0],
                ['type' => 'coupon', 'code' => 'save10', 'amount' => 5.0],
            ],
        );

        $appliedDiscounts = $summary->getAppliedDiscounts();
        $this->assertCount(2, $appliedDiscounts);

        $pricingDiscount = collect($appliedDiscounts)
            ->first(fn(array $discount) => strtolower($discount['type']) === 'pricing_service');

        $this->assertNotNull($pricingDiscount);
        $this->assertSame(15.0, $pricingDiscount['amount']);

        $couponDiscount = collect($appliedDiscounts)
            ->first(fn(array $discount) => strtolower($discount['type']) === 'coupon');

        $this->assertNotNull($couponDiscount);
        $this->assertSame(15.0, $couponDiscount['amount']);
    }
}
