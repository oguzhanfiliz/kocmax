<?php

namespace Tests\Feature\Pricing;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\CustomerPricingTier;
use App\Models\PricingRule;
use App\Services\PricingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PricingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = app(PricingService::class);
    }

    public function test_complete_b2b_dealer_pricing_workflow()
    {
        // 1. Setup: Create dealer tier and user
        $dealerTier = CustomerPricingTier::factory()->create([
            'name' => 'Professional Dealer',
            'type' => 'b2b',
            'discount_percentage' => 20,
            'min_order_amount' => 1000,
            'is_active' => true
        ]);

        $dealer = User::factory()->create([
            'name' => 'ABC Safety Corp',
            'email' => 'orders@abcsafety.com',
            'is_approved_dealer' => true,
            'company_name' => 'ABC Safety Corporation',
            'tax_number' => '1234567890',
            'pricing_tier_id' => $dealerTier->id,
            'custom_discount_percentage' => 5, // Additional 5%
            'credit_limit' => 50000,
            'current_balance' => 10000
        ]);

        // 2. Create product with variants
        $category = Category::factory()->create(['name' => 'Safety Helmets']);
        
        $product = Product::factory()->create([
            'name' => 'Professional Safety Helmet',
            'category_id' => $category->id
        ]);

        $variants = collect([
            ProductVariant::factory()->create([
                'product_id' => $product->id,
                'name' => 'Small - White',
                'price' => 150.00,
                'stock' => 100
            ]),
            ProductVariant::factory()->create([
                'product_id' => $product->id,
                'name' => 'Large - Yellow',
                'price' => 160.00,
                'stock' => 50
            ])
        ]);

        // 3. Create pricing rules
        $bulkRule = PricingRule::factory()->create([
            'name' => 'Bulk Purchase Discount',
            'type' => 'percentage',
            'conditions' => [
                'min_quantity' => 10,
                'customer_type' => 'b2b'
            ],
            'actions' => [
                'discount_percentage' => 8
            ],
            'priority' => 10,
            'is_active' => true
        ]);

        $categoryRule = PricingRule::factory()->create([
            'name' => 'Safety Equipment Special',
            'type' => 'percentage',
            'conditions' => [
                'category_id' => $category->id,
                'customer_type' => 'b2b',
                'min_order_amount' => 2000
            ],
            'actions' => [
                'discount_percentage' => 5
            ],
            'priority' => 5,
            'is_active' => true
        ]);

        // 4. Test small order (below minimum thresholds)
        $smallOrderResult = $this->pricingService->calculatePrice($variants[0], 2, $dealer);
        
        // Should only get custom discount (5%) since order doesn't meet tier minimum
        $expectedSmallPrice = 150.00 * 2 * (1 - 5/100); // 285.00
        $this->assertEquals($expectedSmallPrice, $smallOrderResult->finalPrice->amount);

        // 5. Test large order (meets all criteria)
        $largeOrderResult = $this->pricingService->calculatePrice($variants[0], 15, $dealer);
        
        // Order value: 15 * 150 = 2250 (> 1000 for tier, > 2000 for category rule)
        // Discounts: Tier (20%) + Custom (5%) + Bulk (8%) + Category (5%) = 38%
        $expectedLargePrice = 150.00 * 15 * (1 - 38/100); // 1395.00
        $this->assertEquals($expectedLargePrice, $largeOrderResult->finalPrice->amount);
        $this->assertCount(4, $largeOrderResult->appliedDiscounts);

        // 6. Test validation constraints
        $this->assertTrue($this->pricingService->validatePricing($variants[0], 15, $dealer));
        $this->assertFalse($this->pricingService->validatePricing($variants[1], 100, $dealer)); // Exceeds stock

        // 7. Test available discounts
        $availableDiscounts = $this->pricingService->getAvailableDiscounts($variants[0], $dealer);
        $this->assertGreaterThan(2, $availableDiscounts->count());
    }

    public function test_complete_b2c_customer_pricing_workflow()
    {
        // 1. Setup B2C customer with loyalty tier
        $loyaltyTier = CustomerPricingTier::factory()->create([
            'name' => 'Gold Customer',
            'type' => 'b2c',
            'discount_percentage' => 8,
            'min_order_amount' => 200,
            'is_active' => true
        ]);

        $customer = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'is_approved_dealer' => false,
            'pricing_tier_id' => $loyaltyTier->id,
            'loyalty_points' => 250
        ]);

        // 2. Create products
        $variant = ProductVariant::factory()->create([
            'name' => 'Safety Vest - Medium',
            'price' => 75.00,
            'stock' => 200
        ]);

        // 3. Create B2C-specific rules
        $firstTimeRule = PricingRule::factory()->create([
            'name' => 'Welcome Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c',
                'is_first_time' => true
            ],
            'actions' => [
                'discount_percentage' => 10
            ],
            'is_active' => true
        ]);

        $quantityRule = PricingRule::factory()->create([
            'name' => 'B2C Bulk Discount',
            'type' => 'percentage',
            'conditions' => [
                'min_quantity' => 5,
                'customer_type' => 'b2c'
            ],
            'actions' => [
                'discount_percentage' => 6
            ],
            'is_active' => true
        ]);

        // 4. Test first-time customer scenario
        $context = ['is_first_time' => true];
        $firstTimeResult = $this->pricingService->calculatePrice($variant, 4, $customer, $context);
        
        // Order: 4 * 75 = 300 (> 200 for tier)
        // Discounts: Tier (8%) + First-time (10%) + Bulk (6%) = 24%
        $expectedFirstTimePrice = 300.00 * (1 - 24/100); // 228.00
        $this->assertEquals($expectedFirstTimePrice, $firstTimeResult->finalPrice->amount);

        // 5. Test loyalty points usage
        $loyaltyContext = [
            'use_loyalty_points' => true,
            'loyalty_points_value' => 25 // 25 TL worth
        ];
        $loyaltyResult = $this->pricingService->calculatePrice($variant, 3, $customer, $loyaltyContext);
        
        // Base: 3 * 75 = 225, Tier discount (8%) = 207, Loyalty points (25) = 182
        $expectedLoyaltyPrice = (225.00 * (1 - 8/100)) - 25;
        $this->assertEquals($expectedLoyaltyPrice, $loyaltyResult->finalPrice->amount);

        // 6. Test coupon code application
        $couponRule = PricingRule::factory()->create([
            'name' => 'SUMMER20 Coupon',
            'type' => 'percentage',
            'conditions' => [
                'coupon_code' => 'SUMMER20',
                'customer_type' => 'b2c'
            ],
            'actions' => [
                'discount_percentage' => 15
            ],
            'is_active' => true
        ]);

        $couponContext = ['coupon_code' => 'SUMMER20'];
        $couponResult = $this->pricingService->calculatePrice($variant, 2, $customer, $couponContext);
        
        // Order: 2 * 75 = 150 (< 200, no tier discount)
        // Only coupon discount (15%)
        $expectedCouponPrice = 150.00 * (1 - 15/100); // 127.50
        $this->assertEquals($expectedCouponPrice, $couponResult->finalPrice->amount);
    }

    public function test_guest_user_pricing_workflow()
    {
        // 1. Create guest-applicable promotions
        $variant = ProductVariant::factory()->create([
            'name' => 'Basic Safety Gloves',
            'price' => 25.00,
            'stock' => 500
        ]);

        $guestPromo = PricingRule::factory()->create([
            'name' => 'New Visitor Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'is_first_visit' => true
            ],
            'actions' => [
                'discount_percentage' => 5
            ],
            'is_active' => true
        ]);

        $publicCoupon = PricingRule::factory()->create([
            'name' => 'Public Coupon WELCOME',
            'type' => 'fixed_amount',
            'conditions' => [
                'coupon_code' => 'WELCOME',
                'customer_type' => 'guest'
            ],
            'actions' => [
                'discount_amount' => 5
            ],
            'is_active' => true
        ]);

        // 2. Test base guest pricing
        $baseResult = $this->pricingService->calculatePrice($variant, 1, null);
        $this->assertEquals(25.00, $baseResult->finalPrice->amount);

        // 3. Test first visit discount
        $firstVisitContext = ['is_first_visit' => true];
        $firstVisitResult = $this->pricingService->calculatePrice($variant, 1, null, $firstVisitContext);
        $this->assertEquals(23.75, $firstVisitResult->finalPrice->amount); // 5% off

        // 4. Test public coupon
        $couponContext = ['coupon_code' => 'WELCOME'];
        $couponResult = $this->pricingService->calculatePrice($variant, 1, null, $couponContext);
        $this->assertEquals(20.00, $couponResult->finalPrice->amount); // 5 TL off

        // 5. Test bulk guest purchase
        $bulkGuestRule = PricingRule::factory()->create([
            'name' => 'Guest Bulk Discount',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'guest',
                'min_quantity' => 10
            ],
            'actions' => [
                'discount_percentage' => 8
            ],
            'is_active' => true
        ]);

        $bulkResult = $this->pricingService->calculatePrice($variant, 12, null);
        $expectedBulkPrice = (25.00 * 12) * (1 - 8/100); // 276.00
        $this->assertEquals($expectedBulkPrice, $bulkResult->finalPrice->amount);
    }

    public function test_cross_customer_type_pricing_isolation()
    {
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create customer type specific rules
        $b2bRule = PricingRule::factory()->create([
            'name' => 'B2B Only Discount',
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 20],
            'is_active' => true
        ]);

        $b2cRule = PricingRule::factory()->create([
            'name' => 'B2C Only Discount', 
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $guestRule = PricingRule::factory()->create([
            'name' => 'Guest Only Discount',
            'conditions' => ['customer_type' => 'guest'],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        // Create users
        $b2bUser = User::factory()->create(['is_approved_dealer' => true]);
        $b2cUser = User::factory()->create(['is_approved_dealer' => false]);

        // Test isolation
        $b2bResult = $this->pricingService->calculatePrice($variant, 1, $b2bUser);
        $b2cResult = $this->pricingService->calculatePrice($variant, 1, $b2cUser);
        $guestResult = $this->pricingService->calculatePrice($variant, 1, null);

        // Each should only get their respective discounts
        $this->assertEquals(80.00, $b2bResult->finalPrice->amount);  // 20% B2B
        $this->assertEquals(90.00, $b2cResult->finalPrice->amount);  // 10% B2C
        $this->assertEquals(95.00, $guestResult->finalPrice->amount); // 5% Guest
    }

    public function test_seasonal_campaign_workflow()
    {
        $variant = ProductVariant::factory()->create(['price' => 200.00]);

        // Create seasonal campaign
        $campaign = PricingRule::factory()->create([
            'name' => 'Winter Safety Campaign',
            'type' => 'percentage',
            'conditions' => [
                'campaign_code' => 'WINTER2024',
                'min_order_amount' => 500
            ],
            'actions' => [
                'discount_percentage' => 25
            ],
            'is_active' => true,
            'starts_at' => now()->subDays(5),
            'ends_at' => now()->addDays(10)
        ]);

        $b2cUser = User::factory()->create(['is_approved_dealer' => false]);
        $b2bUser = User::factory()->create(['is_approved_dealer' => true]);

        // Test campaign for different customer types
        $context = ['campaign_code' => 'WINTER2024'];

        // B2C - 3 items = 600 (meets minimum)
        $b2cResult = $this->pricingService->calculatePrice($variant, 3, $b2cUser, $context);
        $this->assertEquals(450.00, $b2cResult->finalPrice->amount); // 25% off

        // B2B - 3 items = 600 (meets minimum)
        $b2bResult = $this->pricingService->calculatePrice($variant, 3, $b2bUser, $context);
        $this->assertEquals(450.00, $b2bResult->finalPrice->amount); // 25% off

        // Guest - 3 items = 600 (meets minimum)
        $guestResult = $this->pricingService->calculatePrice($variant, 3, null, $context);
        $this->assertEquals(450.00, $guestResult->finalPrice->amount); // 25% off

        // Test below minimum order
        $belowMinResult = $this->pricingService->calculatePrice($variant, 2, $b2cUser, $context);
        $this->assertEquals(400.00, $belowMinResult->finalPrice->amount); // No campaign discount
    }

    public function test_pricing_validation_workflow()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 50.00,
            'stock' => 20
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'credit_limit' => 1000,
            'current_balance' => 800 // 200 available credit
        ]);

        // Test stock validation
        $this->assertTrue($this->pricingService->validatePricing($variant, 15, $user));
        $this->assertFalse($this->pricingService->validatePricing($variant, 25, $user)); // Exceeds stock

        // Test credit validation
        $context = ['validate_credit' => true];
        $this->assertTrue($this->pricingService->validatePricing($variant, 3, $user, $context)); // 150 < 200
        $this->assertFalse($this->pricingService->validatePricing($variant, 5, $user, $context)); // 250 > 200

        // Test minimum order validation
        $this->assertTrue($this->pricingService->validatePricing($variant, 1, $user));
        $this->assertFalse($this->pricingService->validatePricing($variant, 0, $user));
        $this->assertFalse($this->pricingService->validatePricing($variant, -1, $user));
    }
}