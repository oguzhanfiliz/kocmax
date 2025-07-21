<?php

namespace Tests\Unit\Pricing\Strategies;

use App\Services\Pricing\B2BPricingStrategy;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\PricingRule;
use App\Models\CustomerPricingTier;
use App\ValueObjects\Pricing\Price;
use App\ValueObjects\Pricing\Discount;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class B2BPricingStrategyTest extends TestCase
{
    use RefreshDatabase;

    private B2BPricingStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new B2BPricingStrategy();
    }

    public function test_calculates_base_b2b_price_with_dealer_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'custom_discount_percentage' => 15
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // Base price 100, with 15% dealer discount = 85
        $this->assertEquals(85.00, $result->finalPrice->amount);
        $this->assertEquals('TRY', $result->finalPrice->currency);
    }

    public function test_applies_customer_pricing_tier_discount()
    {
        $tier = CustomerPricingTier::factory()->create([
            'name' => 'Gold Dealer',
            'type' => 'b2b',
            'discount_percentage' => 20,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'pricing_tier_id' => $tier->id
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // Base price 100, with 20% tier discount = 80
        $this->assertEquals(80.00, $result->finalPrice->amount);
    }

    public function test_applies_quantity_based_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create quantity-based pricing rule
        PricingRule::factory()->create([
            'name' => 'Bulk Discount',
            'type' => 'percentage',
            'conditions' => ['min_quantity' => 10],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 15, $user);

        // Should apply bulk discount for quantity >= 10
        $this->assertLessThan(100.00, $result->finalPrice->amount);
        $this->assertGreaterThan(0, $result->appliedDiscounts->count());
    }

    public function test_applies_amount_based_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create amount-based pricing rule
        PricingRule::factory()->create([
            'name' => 'High Value Discount',
            'type' => 'fixed_amount',
            'conditions' => ['min_order_amount' => 1500],
            'actions' => ['discount_amount' => 100],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 20, $user); // 20 * 100 = 2000

        // Should apply amount-based discount
        $this->assertLessThan(2000.00, $result->finalPrice->amount);
    }

    public function test_respects_minimum_order_amount_from_tier()
    {
        $tier = CustomerPricingTier::factory()->create([
            'name' => 'Premium Dealer',
            'type' => 'b2b',
            'discount_percentage' => 25,
            'min_order_amount' => 500,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'pricing_tier_id' => $tier->id
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Order below minimum
        $result1 = $this->strategy->calculatePrice($variant, 3, $user); // 300 < 500
        
        // Order above minimum
        $result2 = $this->strategy->calculatePrice($variant, 6, $user); // 600 > 500

        // Below minimum should not get full tier discount
        $this->assertGreaterThan($result2->finalPrice->amount, $result1->finalPrice->amount);
    }

    public function test_combines_multiple_discount_types()
    {
        $tier = CustomerPricingTier::factory()->create([
            'name' => 'VIP Dealer',
            'type' => 'b2b',
            'discount_percentage' => 10,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'pricing_tier_id' => $tier->id,
            'custom_discount_percentage' => 5
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create additional pricing rule
        PricingRule::factory()->create([
            'name' => 'B2B Special',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 3],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // Should combine tier discount (10%) + custom discount (5%) + rule discount (3%)
        $expectedDiscount = 100 * (10 + 5 + 3) / 100; // 18% total
        $expectedPrice = 100 - $expectedDiscount;

        $this->assertEquals($expectedPrice, $result->finalPrice->amount);
        $this->assertGreaterThan(1, $result->appliedDiscounts->count());
    }

    public function test_handles_credit_limit_validation()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'credit_limit' => 1000,
            'current_balance' => 800 // Only 200 available credit
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $context = ['validate_credit' => true];

        // Order within credit limit
        $result1 = $this->strategy->calculatePrice($variant, 1, $user, $context); // 100 < 200
        
        // Order exceeding credit limit
        $result2 = $this->strategy->calculatePrice($variant, 5, $user, $context); // 500 > 200

        $this->assertTrue($result1->isValid ?? true);
        $this->assertFalse($result2->isValid ?? true);
    }

    public function test_applies_seasonal_discounts()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create time-based pricing rule
        PricingRule::factory()->create([
            'name' => 'Winter Sale',
            'type' => 'percentage',
            'conditions' => ['season' => 'winter'],
            'actions' => ['discount_percentage' => 15],
            'is_active' => true,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(10)
        ]);

        $context = ['season' => 'winter'];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        $this->assertLessThan(100.00, $result->finalPrice->amount);
        $this->assertStringContains('Winter Sale', 
            $result->appliedDiscounts->pluck('name')->implode(', ')
        );
    }

    public function test_respects_discount_priority_ordering()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create rules with different priorities
        PricingRule::factory()->create([
            'name' => 'Low Priority',
            'priority' => 1,
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        PricingRule::factory()->create([
            'name' => 'High Priority',
            'priority' => 10,
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 8],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // High priority discount should be applied first
        $firstDiscount = $result->appliedDiscounts->first();
        $this->assertEquals('High Priority', $firstDiscount->name ?? '');
    }

    public function test_handles_inactive_pricing_rules()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create inactive rule
        PricingRule::factory()->create([
            'name' => 'Inactive Rule',
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 20],
            'is_active' => false
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // Inactive rule should not be applied
        $this->assertEquals(100.00, $result->finalPrice->amount);
        $this->assertEmpty($result->appliedDiscounts);
    }

    public function test_calculates_price_with_tax_inclusion()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => true
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $context = ['include_tax' => true, 'tax_rate' => 18];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        // Should include 18% tax
        $expectedPriceWithTax = 100 * 1.18;
        $this->assertEquals($expectedPriceWithTax, $result->finalPrice->amount);
    }
}