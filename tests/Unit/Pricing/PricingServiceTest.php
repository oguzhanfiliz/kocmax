<?php

namespace Tests\Unit\Pricing;

use App\Services\PricingService;
use App\Services\Pricing\PriceEngine;
use App\Services\Pricing\CustomerTypeDetector;
use App\Models\User;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\PricingRule;
use App\Models\CustomerPricingTier;
use App\ValueObjects\Pricing\PriceResult;
use App\ValueObjects\Pricing\Price;
use App\Enums\Pricing\CustomerType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;
    private PriceEngine $mockPriceEngine;
    private CustomerTypeDetector $mockDetector;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockPriceEngine = Mockery::mock(PriceEngine::class);
        $this->mockDetector = Mockery::mock(CustomerTypeDetector::class);
        
        $this->pricingService = new PricingService(
            $this->mockPriceEngine,
            $this->mockDetector
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculates_price_for_b2c_customer()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(95.00, 'TRY'),
            collect(),
            ['customer_type' => CustomerType::B2C]
        );

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2C);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->with($variant, 1, CustomerType::B2C, $user, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, $user);

        $this->assertEquals(95.00, $result->finalPrice->amount);
        $this->assertEquals('TRY', $result->finalPrice->currency);
    }

    public function test_calculates_price_for_b2b_customer_with_quantity_discount()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(80.00, 'TRY'), // 20% quantity discount
            collect(),
            ['customer_type' => CustomerType::B2B, 'quantity_discount' => 20]
        );

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2B);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->with($variant, 100, CustomerType::B2B, $user, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 100, $user);

        $this->assertEquals(80.00, $result->finalPrice->amount);
    }

    public function test_calculates_price_for_guest_user()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(100.00, 'TRY'),
            collect(),
            ['customer_type' => CustomerType::GUEST]
        );

        $this->mockDetector
            ->shouldReceive('detect')
            ->with(null, [])
            ->once()
            ->andReturn(CustomerType::GUEST);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->with($variant, 1, CustomerType::GUEST, null, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, null);

        $this->assertEquals(100.00, $result->finalPrice->amount);
    }

    public function test_validates_pricing_with_valid_parameters()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 50
        ]);

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2C);

        $isValid = $this->pricingService->validatePricing($variant, 10, $user);

        $this->assertTrue($isValid);
    }

    public function test_validates_pricing_with_insufficient_stock()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00,
            'stock_quantity' => 5
        ]);

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2C);

        $isValid = $this->pricingService->validatePricing($variant, 10, $user);

        $this->assertFalse($isValid);
    }

    public function test_validates_pricing_with_zero_quantity()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $isValid = $this->pricingService->validatePricing($variant, 0, $user);

        $this->assertFalse($isValid);
    }

    public function test_validates_pricing_with_negative_quantity()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $isValid = $this->pricingService->validatePricing($variant, -1, $user);

        $this->assertFalse($isValid);
    }

    public function test_gets_available_discounts_for_customer()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create();

        // Create pricing rules
        $rule1 = PricingRule::factory()->create([
            'name' => 'Bulk Discount',
            'conditions' => ['min_quantity' => 10],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        $rule2 = PricingRule::factory()->create([
            'name' => 'Dealer Discount',
            'conditions' => ['customer_type' => 'b2b'],
            'actions' => ['discount_percentage' => 10],
            'is_active' => true
        ]);

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2B);

        $discounts = $this->pricingService->getAvailableDiscounts($variant, $user);

        $this->assertGreaterThan(0, $discounts->count());
    }

    public function test_calculates_price_with_custom_context()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $context = [
            'campaign_code' => 'SUMMER2024',
            'referral_discount' => true
        ];

        $expectedResult = new PriceResult(
            new Price(85.00, 'TRY'),
            collect(),
            array_merge(['customer_type' => CustomerType::B2C], $context)
        );

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, $context)
            ->once()
            ->andReturn(CustomerType::B2C);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->with($variant, 1, CustomerType::B2C, $user, $context)
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, $user, $context);

        $this->assertEquals(85.00, $result->finalPrice->amount);
        $this->assertEquals('SUMMER2024', $result->context['campaign_code']);
    }

    public function test_handles_pricing_rule_priority()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        // Create rules with different priorities
        $lowPriority = PricingRule::factory()->create([
            'name' => 'Low Priority Rule',
            'priority' => 1,
            'conditions' => ['min_quantity' => 1],
            'actions' => ['discount_percentage' => 5],
            'is_active' => true
        ]);

        $highPriority = PricingRule::factory()->create([
            'name' => 'High Priority Rule', 
            'priority' => 10,
            'conditions' => ['min_quantity' => 1],
            'actions' => ['discount_percentage' => 15],
            'is_active' => true
        ]);

        $this->mockDetector
            ->shouldReceive('detect')
            ->with($user, [])
            ->once()
            ->andReturn(CustomerType::B2C);

        $discounts = $this->pricingService->getAvailableDiscounts($variant, $user);

        // High priority rule should come first
        $sortedDiscounts = $discounts->sortByDesc('priority');
        $firstDiscount = $sortedDiscounts->first();
        
        $this->assertEquals('High Priority Rule', $firstDiscount->name);
    }

    public function test_calculates_bulk_pricing_correctly()
    {
        $user = User::factory()->create();
        $variants = collect([
            ProductVariant::factory()->create(['price' => 100.00]),
            ProductVariant::factory()->create(['price' => 150.00]),
            ProductVariant::factory()->create(['price' => 200.00]),
        ]);

        $quantities = [2, 3, 1];

        $this->mockDetector
            ->shouldReceive('detect')
            ->times(3)
            ->andReturn(CustomerType::B2C);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->times(3)
            ->andReturn(new PriceResult(new Price(95.00, 'TRY'), collect(), []));

        $results = $this->pricingService->calculateBulkPricing($variants, $quantities, $user);

        $this->assertCount(3, $results);
        $this->assertInstanceOf(PriceResult::class, $results[0]);
    }

    public function test_caches_pricing_calculations()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(95.00, 'TRY'),
            collect(),
            ['customer_type' => CustomerType::B2C]
        );

        $this->mockDetector
            ->shouldReceive('detect')
            ->once()
            ->andReturn(CustomerType::B2C);

        $this->mockPriceEngine
            ->shouldReceive('calculate')
            ->once()
            ->andReturn($expectedResult);

        // First call
        $result1 = $this->pricingService->calculatePrice($variant, 1, $user);
        
        // Second call should use cache (mocks called only once)
        $result2 = $this->pricingService->calculatePrice($variant, 1, $user);

        $this->assertEquals($result1->finalPrice->amount, $result2->finalPrice->amount);
    }
}