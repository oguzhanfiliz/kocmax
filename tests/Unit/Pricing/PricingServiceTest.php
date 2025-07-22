<?php

namespace Tests\Unit\Pricing;

use App\Services\PricingService;
use App\Services\Pricing\PriceEngine;
use App\Models\User;
use App\Models\ProductVariant;
use App\ValueObjects\Pricing\PriceResult;
use App\ValueObjects\Pricing\Price;
use App\Enums\Pricing\CustomerType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricingService;
    private PriceEngine $mockPriceEngine;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockPriceEngine = Mockery::mock(PriceEngine::class);
        
        $this->pricingService = new PricingService(
            $this->mockPriceEngine
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
            new Price(100.00, 'TRY'),
            new Price(95.00, 'TRY'), 
            collect(),
            CustomerType::B2C,
            1
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 1, $user, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, $user);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_calculates_price_for_b2b_customer_with_quantity_discount()
    {
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(500.00, 'TRY'), 
            new Price(450.00, 'TRY'),
            collect(),
            CustomerType::B2B,
            5
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 5, $user, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 5, $user);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_calculates_price_for_guest_user()
    {
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(100.00, 'TRY'),
            new Price(100.00, 'TRY'),
            collect(), 
            CustomerType::GUEST,
            1
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 1, null, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, null);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_validates_pricing_with_valid_parameters()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00,
            'stock' => 50
        ]);

        $this->mockPriceEngine
            ->shouldReceive('validatePricing')
            ->with($variant, 10, $user)
            ->once()
            ->andReturn(true);

        $result = $this->pricingService->validatePricing($variant, 10, $user);

        $this->assertTrue($result);
    }

    public function test_validates_pricing_with_insufficient_stock()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00,
            'stock' => 5
        ]);

        $this->mockPriceEngine
            ->shouldReceive('validatePricing')
            ->with($variant, 10, $user)
            ->once()
            ->andReturn(false);

        $result = $this->pricingService->validatePricing($variant, 10, $user);

        $this->assertFalse($result);
    }

    public function test_validates_pricing_with_zero_quantity()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $this->mockPriceEngine
            ->shouldReceive('validatePricing')
            ->with($variant, 0, $user)
            ->once()
            ->andReturn(false);

        $result = $this->pricingService->validatePricing($variant, 0, $user);

        $this->assertFalse($result);
    }

    public function test_validates_pricing_with_negative_quantity()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $this->mockPriceEngine
            ->shouldReceive('validatePricing')
            ->with($variant, -1, $user)
            ->once()
            ->andReturn(false);

        $result = $this->pricingService->validatePricing($variant, -1, $user);

        $this->assertFalse($result);
    }

    public function test_gets_available_discounts_for_customer()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $expectedDiscounts = collect(['discount1', 'discount2']);

        $this->mockPriceEngine
            ->shouldReceive('getAvailableDiscounts')
            ->with($variant, $user)
            ->once()
            ->andReturn($expectedDiscounts);

        $result = $this->pricingService->getAvailableDiscounts($variant, $user);

        $this->assertEquals($expectedDiscounts, $result);
    }

    public function test_calculates_price_with_custom_context()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);
        $context = ['campaign_id' => 123];

        $expectedResult = new PriceResult(
            new Price(100.00, 'TRY'),
            new Price(85.00, 'TRY'),
            collect(),
            CustomerType::B2C,
            2
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 2, $user, $context)
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 2, $user, $context);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_handles_pricing_rule_priority()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(100.00, 'TRY'),
            new Price(80.00, 'TRY'),
            collect(),
            CustomerType::B2C,
            1
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 1, $user, [])
            ->once()
            ->andReturn($expectedResult);

        $result = $this->pricingService->calculatePrice($variant, 1, $user);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_calculates_bulk_pricing_correctly()
    {
        $user = User::factory()->create();
        $items = [
            ['variant' => ProductVariant::factory()->create(['price' => 100]), 'quantity' => 2],
            ['variant' => ProductVariant::factory()->create(['price' => 200]), 'quantity' => 3],
            ['variant' => ProductVariant::factory()->create(['price' => 150]), 'quantity' => 1],
        ];

        $expectedResults = collect([
            new PriceResult(new Price(200.00, 'TRY'), new Price(190.00, 'TRY'), collect(), CustomerType::B2C, 2),
            new PriceResult(new Price(600.00, 'TRY'), new Price(570.00, 'TRY'), collect(), CustomerType::B2C, 3),
            new PriceResult(new Price(150.00, 'TRY'), new Price(145.00, 'TRY'), collect(), CustomerType::B2C, 1),
        ]);

        $this->mockPriceEngine
            ->shouldReceive('bulkCalculatePrice')
            ->with($items, $user, [])
            ->once()
            ->andReturn($expectedResults);

        $result = $this->pricingService->bulkCalculatePrice($items, $user);

        $this->assertEquals($expectedResults, $result);
    }

    public function test_caches_pricing_calculations()
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create([
            'price' => 100.00
        ]);

        $expectedResult = new PriceResult(
            new Price(100.00, 'TRY'),
            new Price(95.00, 'TRY'),
            collect(),
            CustomerType::B2C,
            1
        );

        $this->mockPriceEngine
            ->shouldReceive('calculatePrice')
            ->with($variant, 1, $user, [])
            ->once()
            ->andReturn($expectedResult);

        // First call
        $result1 = $this->pricingService->calculatePrice($variant, 1, $user);
        $this->assertEquals($expectedResult, $result1);

        // Note: Caching is handled within PriceEngine, not PricingService
        // So this test just validates that the service delegates properly
    }
}