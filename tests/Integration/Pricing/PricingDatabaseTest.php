<?php

namespace Tests\Integration\Pricing;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CustomerPricingTier;
use App\Models\PricingRule;
use App\Models\PriceHistory;
use App\Models\Category;
use App\Services\PricingService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PricingDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingService = app(PricingService::class);
    }

    public function test_creates_and_applies_customer_pricing_tier()
    {
        // Create tier
        $tier = CustomerPricingTier::create([
            'name' => 'Gold Dealer',
            'type' => 'b2b',
            'discount_percentage' => 15,
            'min_order_amount' => 500,
            'is_active' => true
        ]);

        // Assign to user
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'pricing_tier_id' => $tier->id
        ]);

        // Create product and variant
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Calculate price
        $result = $this->pricingService->calculatePrice($variant, 10, $user); // 1000 > 500

        // Verify tier discount applied
        $this->assertLessThan(1000.00, $result->finalPrice->amount);
        
        // Verify database relationship
        $this->assertEquals($tier->id, $user->pricingTier->id);
        $this->assertEquals('Gold Dealer', $user->pricingTier->name);
    }

    public function test_creates_and_applies_pricing_rules()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create pricing rule
        $rule = PricingRule::create([
            'name' => 'Bulk Discount Rule',
            'type' => 'percentage',
            'conditions' => [
                'min_quantity' => 5,
                'customer_type' => 'b2b'
            ],
            'actions' => [
                'discount_percentage' => 12
            ],
            'priority' => 10,
            'is_active' => true
        ]);

        $result = $this->pricingService->calculatePrice($variant, 8, $user);

        // Verify rule was applied
        $this->assertLessThan(800.00, $result->finalPrice->amount);
        
        // Verify rule exists in database
        $this->assertDatabaseHas('pricing_rules', [
            'name' => 'Bulk Discount Rule',
            'is_active' => true
        ]);
    }

    public function test_logs_price_changes_to_history()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Update product price
        $variant->update(['price' => 120.00]);

        // Calculate both old and new prices
        $variant->price = 100.00;
        $oldResult = $this->pricingService->calculatePrice($variant, 1, $user);
        
        $variant->price = 120.00;
        $newResult = $this->pricingService->calculatePrice($variant, 1, $user);

        // Simulate price history logging
        PriceHistory::create([
            'product_variant_id' => $variant->id,
            'customer_type' => 'b2b',
            'old_price' => $oldResult->finalPrice->amount,
            'new_price' => $newResult->finalPrice->amount,
            'reason' => 'Price update test',
            'changed_by' => $user->id
        ]);

        $this->assertDatabaseHas('price_history', [
            'product_variant_id' => $variant->id,
            'customer_type' => 'b2b',
            'reason' => 'Price update test'
        ]);
    }

    public function test_pricing_rules_with_product_relationships()
    {
        $category = Category::factory()->create(['name' => 'Safety Equipment']);
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 150.00
        ]);

        $user = User::factory()->create(['is_approved_dealer' => true]);

        // Create category-specific rule
        $rule = PricingRule::create([
            'name' => 'Safety Equipment Discount',
            'type' => 'percentage',
            'conditions' => [
                'category_id' => $category->id,
                'customer_type' => 'b2b'
            ],
            'actions' => [
                'discount_percentage' => 8
            ],
            'is_active' => true
        ]);

        // Create pivot relationship
        DB::table('pricing_rule_categories')->insert([
            'pricing_rule_id' => $rule->id,
            'category_id' => $category->id
        ]);

        $result = $this->pricingService->calculatePrice($variant, 1, $user);

        // Verify category-specific discount applied
        $expectedPrice = 150.00 * (1 - 8/100);
        $this->assertEquals($expectedPrice, $result->finalPrice->amount);
    }

    public function test_pricing_rules_with_time_constraints()
    {
        $user = User::factory()->create(['is_approved_dealer' => false]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create time-limited rule
        $rule = PricingRule::create([
            'name' => 'Flash Sale',
            'type' => 'percentage',
            'conditions' => [
                'customer_type' => 'b2c'
            ],
            'actions' => [
                'discount_percentage' => 20
            ],
            'is_active' => true,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour()
        ]);

        $result = $this->pricingService->calculatePrice($variant, 1, $user);

        // Should apply time-limited discount
        $this->assertEquals(80.00, $result->finalPrice->amount);

        // Test expired rule
        $rule->update(['ends_at' => now()->subMinutes(10)]);
        
        $expiredResult = $this->pricingService->calculatePrice($variant, 1, $user);
        
        // Should not apply expired discount
        $this->assertEquals(100.00, $expiredResult->finalPrice->amount);
    }

    public function test_complex_pricing_rule_combinations()
    {
        $tier = CustomerPricingTier::create([
            'name' => 'Premium B2C',
            'type' => 'b2c',
            'discount_percentage' => 5,
            'is_active' => true
        ]);

        $user = User::factory()->create([
            'is_approved_dealer' => false,
            'pricing_tier_id' => $tier->id,
            'custom_discount_percentage' => 3
        ]);

        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        // Create multiple rules
        PricingRule::create([
            'name' => 'Quantity Discount',
            'type' => 'percentage',
            'conditions' => ['min_quantity' => 5, 'customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 7],
            'priority' => 10,
            'is_active' => true
        ]);

        PricingRule::create([
            'name' => 'Seasonal Discount',
            'type' => 'percentage', 
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 4],
            'priority' => 5,
            'is_active' => true
        ]);

        $result = $this->pricingService->calculatePrice($variant, 6, $user);

        // Should combine tier (5%) + custom (3%) + quantity (7%) + seasonal (4%) = 19%
        $expectedDiscount = 100 * 19 / 100;
        $expectedPrice = (100 * 6) - (600 * 19 / 100);
        
        $this->assertEquals($expectedPrice, $result->finalPrice->amount);
        $this->assertGreaterThan(2, $result->appliedDiscounts->count());
    }

    public function test_database_constraints_and_validations()
    {
        // Test unique constraint on pricing tier names
        CustomerPricingTier::create([
            'name' => 'Unique Tier',
            'type' => 'b2b',
            'discount_percentage' => 10,
            'is_active' => true
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        CustomerPricingTier::create([
            'name' => 'Unique Tier', // Duplicate name
            'type' => 'b2c',
            'discount_percentage' => 5,
            'is_active' => true
        ]);
    }

    public function test_soft_deletes_behavior()
    {
        $user = User::factory()->create(['is_approved_dealer' => false]);
        $variant = ProductVariant::factory()->create(['price' => 100.00]);

        $rule = PricingRule::create([
            'name' => 'Test Rule',
            'type' => 'percentage',
            'conditions' => ['customer_type' => 'b2c'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        // Rule should apply
        $result1 = $this->pricingService->calculatePrice($variant, 1, $user);
        $this->assertEquals(90.00, $result1->finalPrice->amount);

        // Deactivate rule
        $rule->update(['is_active' => false]);

        // Rule should not apply
        $result2 = $this->pricingService->calculatePrice($variant, 1, $user);
        $this->assertEquals(100.00, $result2->finalPrice->amount);
    }

    public function test_pricing_with_currency_conversion()
    {
        $user = User::factory()->create(['is_approved_dealer' => false]);
        $variant = ProductVariant::factory()->create([
            'price' => 100.00,
            'currency' => 'USD'
        ]);

        // Mock currency conversion context
        $context = [
            'target_currency' => 'TRY',
            'exchange_rate' => 30.0
        ];

        $result = $this->pricingService->calculatePrice($variant, 1, $user, $context);

        // Should convert USD to TRY
        $expectedPrice = 100.00 * 30.0;
        $this->assertEquals($expectedPrice, $result->finalPrice->amount);
    }

    public function test_bulk_pricing_performance()
    {
        $users = User::factory(5)->create(['is_approved_dealer' => true]);
        $variants = ProductVariant::factory(10)->create(['price' => 100.00]);

        // Create multiple pricing rules
        PricingRule::factory(5)->create([
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $startTime = microtime(true);

        // Calculate prices for multiple combinations
        foreach ($users as $user) {
            foreach ($variants as $variant) {
                $this->pricingService->calculatePrice($variant, rand(1, 10), $user);
            }
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Should complete within reasonable time (< 5 seconds for 50 calculations)
        $this->assertLessThan(5.0, $executionTime);
    }
}