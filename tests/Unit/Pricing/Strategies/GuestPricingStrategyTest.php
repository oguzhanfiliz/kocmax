<?php

namespace Tests\Unit\Pricing\Strategies;

use App\Services\Pricing\GuestPricingStrategy;
use App\Models\ProductVariant;
use App\Models\PricingRule;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestPricingStrategyTest extends TestCase
{
    use RefreshDatabase;

    private GuestPricingStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new GuestPricingStrategy();
    }

    public function test_returns_base_price_for_guest_users()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, null);

        // Guests get full list price
        $this->assertEquals(100.00, $result->finalPrice->amount);
        $this->assertEquals('TRY', $result->finalPrice->currency);
        $this->assertEmpty($result->appliedDiscounts);
    }

    public function test_applies_anonymous_promotional_discount()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create guest-applicable promotion
        PricingRule::factory()->create([
            'name' => 'Guest Welcome Offer',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'is_promotional' => true
            ],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        $context = ['is_promotional' => true];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(95.00, $result->finalPrice->amount);
        $this->assertCount(1, $result->appliedDiscounts);
    }

    public function test_applies_public_coupon_code()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Public Coupon NEW10',
            'type' => 'percentage',
            'conditions' => [
                'coupon_code' => 'NEW10',
                'customer_type' => 'guest'
            ],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $context = ['coupon_code' => 'NEW10'];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(90.00, $result->finalPrice->amount);
    }

    public function test_applies_first_visitor_discount()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'First Visit Discount',
            'type' => 'fixed_amount',
            'conditions' => [
                'customer_type' => 'guest',
                'is_first_visit' => true
            ],
            'actions' => ['discount_amount' => 15],
            'is_active' => true
        ]);

        $context = ['is_first_visit' => true];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(85.00, $result->finalPrice->amount);
    }

    public function test_applies_quantity_based_guest_discount()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 50.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Guest Bulk Purchase',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'min_quantity' => 3
            ],
            'actions' => ['discount_percentage' => 7],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 4, null);

        // 4 items * 50 = 200, with 7% discount = 186
        $this->assertEquals(186.00, $result->finalPrice->amount);
    }

    public function test_applies_seasonal_guest_promotion()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Holiday Special',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'season' => 'holiday'
            ],
            'actions' => ['discount_percentage' => 12],
            'is_active' => true,
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->addDays(7)
        ]);

        $context = ['season' => 'holiday'];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(88.00, $result->finalPrice->amount);
    }

    public function test_ignores_customer_specific_discounts()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create B2B and B2C specific rules that should be ignored
        PricingRule::factory()->create([
            'name' => 'B2B Only Discount',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 20],
            'is_active' => true
        ]);

        PricingRule::factory()->create([
            'name' => 'B2C Only Discount',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 15],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, null);

        // Guest should not get any of these discounts
        $this->assertEquals(100.00, $result->finalPrice->amount);
        $this->assertEmpty($result->appliedDiscounts);
    }

    public function test_handles_limited_time_flash_sale()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Flash Sale 30min',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'is_flash_sale' => true
            ],
            'actions' => ['discount_percentage' => 20],
            'is_active' => true,
            'starts_at' => now()->subMinutes(15),
            'ends_at' => now()->addMinutes(15) // 30 minute window
        ]);

        $context = ['is_flash_sale' => true];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(80.00, $result->finalPrice->amount);
    }

    public function test_applies_newsletter_signup_discount()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Newsletter Signup Bonus',
            'type' => 'fixed_amount',
            'conditions' => [
                'customer_type' => 'guest',
                'newsletter_signup' => true
            ],
            'actions' => ['discount_amount' => 10],
            'is_active' => true
        ]);

        $context = ['newsletter_signup' => true];
        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        $this->assertEquals(90.00, $result->finalPrice->amount);
    }

    public function test_combines_multiple_guest_applicable_discounts()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Multiple guest rules
        PricingRule::factory()->create([
            'name' => 'Guest Promo 1',
            'type' => 'percentage',
            'priority' => 10,
            'conditions' => ['customer_type' => 'guest'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        PricingRule::factory()->create([
            'name' => 'Guest Promo 2',
            'type' => 'fixed_amount',
            'priority' => 5,
            'conditions' => ['customer_type' => 'guest'],
            'actions' => ['discount_amount' => 8],
            'is_active' => true
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, null);

        // Should combine both discounts
        $this->assertLessThan(100.00, $result->finalPrice->amount);
        $this->assertCount(2, $result->appliedDiscounts);
    }

    public function test_respects_minimum_order_amount_for_guests()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 30.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Guest High Value Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'min_order_amount' => 100
            ],
            'actions' => ['discount_percentage' => 8],
            'is_active' => true
        ]);

        // Order below minimum (30 * 2 = 60 < 100)
        $result1 = $this->strategy->calculatePrice($variant, 2, null);
        
        // Order above minimum (30 * 4 = 120 > 100)
        $result2 = $this->strategy->calculatePrice($variant, 4, null);

        // Below minimum should not get discount
        $this->assertEquals(60.00, $result1->finalPrice->amount);
        
        // Above minimum should get discount
        $this->assertLessThan(120.00, $result2->finalPrice->amount);
    }

    public function test_handles_expired_guest_promotions()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        PricingRule::factory()->create([
            'name' => 'Expired Guest Promo',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'guest'],
            'actions' => ['discount_percentage' => 15],
            'is_active' => true,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDays(1) // Expired yesterday
        ]);

        $result = $this->strategy->calculatePrice($variant, 1, null);

        // Expired promotion should not apply
        $this->assertEquals(100.00, $result->finalPrice->amount);
        $this->assertEmpty($result->appliedDiscounts);
    }

    public function test_calculates_price_with_tax_for_guest()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $context = [
            'include_tax' => true,
            'tax_rate' => 18
        ];

        $result = $this->strategy->calculatePrice($variant, 1, null, $context);

        // Should include 18% tax
        $this->assertEquals(118.00, $result->finalPrice->amount);
    }
}