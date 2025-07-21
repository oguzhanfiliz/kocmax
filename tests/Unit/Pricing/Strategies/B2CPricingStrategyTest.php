<?php

namespace Tests\Unit\Pricing\Strategies;

use App\Services\Pricing\B2CPricingStrategy;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\PricingRule;
use App\Models\CustomerPricingTier;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class B2CPricingStrategyTest extends TestCase
{
    use RefreshDatabase;

    private B2CPricingStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new B2CPricingStrategy();
    }

    public function test_calculates_base_b2c_price()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // B2C users get list price by default
        $this->assertEquals(100.00, $result->finalPrice->amount);
        $this->assertEquals('TRY', $result->finalPrice->currency);
    }

    public function test_applies_b2c_customer_tier_discount()
    {
        $tier = CustomerPricingTier::factory()->create([
            'name' => 'Loyal Customer',
            'type' => 'b2c',
            'discount_percentage' => 5,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'pricing_tier_id' => $tier->id
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // 5% discount for loyal B2C customer
        $this->assertEquals(95.00, $result->finalPrice->amount);
    }

    public function test_applies_quantity_discount_for_b2c()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 50.00
        ]);

        // Create B2C quantity rule
        PricingRule::factory()->create([
            'name' => 'B2C Bulk Discount',
            'type' => 'percentage',
            'conditions' => [
                'min_quantity' => 5,
                'customer_type' => 'b2c'
            ],
            'actions' => ['discount_percentage' => 8],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 6, $user);

        // 8% discount on 6 items (50 * 6 = 300, discount = 24, final = 276)
        $expectedPrice = (50 * 6) * (1 - 8/100);
        $this->assertEquals($expectedPrice, $result->finalPrice->amount);
    }

    public function test_applies_first_time_customer_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'created_at' => now() // New customer
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create first-time customer rule
        PricingRule::factory()->create([
            'name' => 'Welcome Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c',
                'is_first_time' => true
            ],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $context = ['is_first_time' => true];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        $this->assertEquals(90.00, $result->finalPrice->amount);
        $this->assertStringContains('Welcome', 
            $result->appliedDiscounts->pluck('name')->implode(', ')
        );
    }

    public function test_applies_loyalty_points_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'loyalty_points' => 500
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $context = [
            'use_loyalty_points' => true,
            'loyalty_points_value' => 25 // 25 TL worth of points
        ];

        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        // Should deduct loyalty points value from price
        $this->assertEquals(75.00, $result->finalPrice->amount);
    }

    public function test_applies_student_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create student discount rule
        PricingRule::factory()->create([
            'name' => 'Student Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c',
                'is_student' => true
            ],
            'actions' => ['discount_percentage' => 15],
            'is_active' => true
        ]);

        $context = ['is_student' => true];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        $this->assertEquals(85.00, $result->finalPrice->amount);
    }

    public function test_handles_coupon_code_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create coupon code rule
        PricingRule::factory()->create([
            'name' => 'Coupon SAVE20',
            'type' => 'percentage',
            'conditions' => [
                'coupon_code' => 'SAVE20',
                'customer_type' => 'b2c'
            ],
            'actions' => ['discount_percentage' => 20],
            'is_active' => true
        ]);

        $context = ['coupon_code' => 'SAVE20'];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        $this->assertEquals(80.00, $result->finalPrice->amount);
    }

    public function test_applies_referral_discount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Referral Bonus',
            'type' => 'fixed_amount',
            'conditions' => [
                'customer_type' => 'b2c',
                'has_referral' => true
            ],
            'actions' => ['discount_amount' => 15],
            'is_active' => true
        ]);

        $context = ['has_referral' => true, 'referral_code' => 'REF123'];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        // Fixed amount discount
        $this->assertEquals(85.00, $result->finalPrice->amount);
    }

    public function test_respects_maximum_discount_limit()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create multiple high discount rules
        PricingRule::factory()->create([
            'name' => 'High Discount 1',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 30],
            'is_active' => true
        ]);

        PricingRule::factory()->create([
            'name' => 'High Discount 2',
            'type' => 'percentage', 
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 40],
            'is_active' => true
        ]);

        $context = ['max_discount_percentage' => 50];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        // Total discount should not exceed 50%
        $this->assertGreaterThanOrEqual(50.00, $result->finalPrice->amount);
    }

    public function test_handles_seasonal_b2c_promotions()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Summer Sale',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c',
                'promotion_code' => 'SUMMER2024'
            ],
            'actions' => ['discount_percentage' => 25],
            'is_active' => true,
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(5)
        ]);

        $context = ['promotion_code' => 'SUMMER2024'];
        $result = $this->strategy->calculatePrice($variant, 1, $user, $context);

        $this->assertEquals(75.00, $result->finalPrice->amount);
    }

    public function test_combines_tier_and_promotional_discounts()
    {
        $tier = CustomerPricingTier::factory()->create([
            'name' => 'Premium B2C',
            'type' => 'b2c',
            'discount_percentage' => 10,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'pricing_tier_id' => $tier->id
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Flash Sale',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, $user);

        // 10% tier + 5% promotional = 15% total
        $this->assertEquals(85.00, $result->finalPrice->amount);
        $this->assertGreaterThan(1, $result->appliedDiscounts->count());
    }

    public function test_validates_minimum_purchase_amount()
    {
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);

        $variant = ProductVariant::factory()->create([
            'price' => 25.00
        ]);

        PricingRule::factory()->create([
            'name' => 'High Value Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c',
                'min_order_amount' => 100
            ],
            'actions' => ['discount_percentage' => 12],
            'is_active' => true
        ]);

        // Order below minimum (25 * 2 = 50 < 100)
        $result1 = $this->strategy->calculatePrice($variant, 2, $user);
        
        // Order above minimum (25 * 5 = 125 > 100)  
        $result2 = $this->strategy->calculatePrice($variant, 5, $user);

        // First order should not get discount
        $this->assertEquals(50.00, $result1->finalPrice->amount);
        
        // Second order should get discount
        $this->assertLessThan(125.00, $result2->finalPrice->amount);
    }
}