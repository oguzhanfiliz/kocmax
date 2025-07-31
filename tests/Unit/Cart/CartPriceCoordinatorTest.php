<?php

declare(strict_types=1);

namespace Tests\Unit\Cart;

use App\Services\Cart\CartPriceCoordinator;
use App\Services\PricingService;
use App\Services\Pricing\CustomerTypeDetector;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Pricing\Price;
use App\ValueObjects\Pricing\PriceResult;
use App\ValueObjects\Pricing\Discount;
use App\Enums\CustomerType;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CartPriceCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    private CartPriceCoordinator $priceCoordinator;
    private $mockPricingService;
    private $mockCustomerTypeDetector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockPricingService = Mockery::mock(PricingService::class);
        $this->mockCustomerTypeDetector = Mockery::mock(CustomerTypeDetector::class);

        $this->priceCoordinator = new CartPriceCoordinator(
            $this->mockPricingService,
            $this->mockCustomerTypeDetector
        );
    }

    public function test_updates_cart_pricing_successfully(): void
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100.00]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);

        // Mock customer type detection
        $this->mockCustomerTypeDetector->shouldReceive('detect')
            ->once()
            ->with($user)
            ->andReturn(CustomerType::B2C);

        // Mock pricing calculation for the item
        $basePrice = new Price(100.00);
        $finalPrice = new Price(90.00);
        $discount = new Discount(10.00, 'Test discount');
        $priceResult = new PriceResult($basePrice, $finalPrice, $discount, CustomerType::B2C);

        $this->mockPricingService->shouldReceive('calculatePrice')
            ->once()
            ->with($variant, 2, $user)
            ->andReturn($priceResult);

        // Act
        $result = $this->priceCoordinator->updateCartPricing($cart);

        // Assert
        $this->assertInstanceOf(CartSummary::class, $result);
        
        // Verify cart was updated
        $cart = $cart->fresh();
        $this->assertEquals('b2c', $cart->customer_type);
        $this->assertNotNull($cart->pricing_calculated_at);
        $this->assertNotNull($cart->last_pricing_update);
    }

    public function test_calculates_cart_summary_with_multiple_items(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product1 = Product::factory()->create();
        $variant1 = ProductVariant::factory()->create(['product_id' => $product1->id, 'price' => 100.00]);
        $item1 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant1->id,
            'quantity' => 2,
            'calculated_price' => 90.00, // After discount
            'total_discount' => 20.00
        ]);

        $product2 = Product::factory()->create();
        $variant2 = ProductVariant::factory()->create(['product_id' => $product2->id, 'price' => 50.00]);
        $item2 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant2->id,
            'quantity' => 1,
            'calculated_price' => 45.00, // After discount
            'total_discount' => 5.00
        ]);

        $cart->setRelation('items', collect([$item1, $item2]));
        $product1->setRelation('name', 'Product 1');
        $product2->setRelation('name', 'Product 2');
        $item1->setRelation('product', $product1);
        $item2->setRelation('product', $product2);

        // Act
        $result = $this->priceCoordinator->calculateCartSummary($cart);

        // Assert
        $this->assertInstanceOf(CartSummary::class, $result);
        $this->assertEquals(225.00, $result->getSubtotal()); // (90*2) + (45*1)
        $this->assertEquals(25.00, $result->getDiscount()); // 20 + 5
        $this->assertEquals(200.00, $result->getTotal()); // 225 - 25
        $this->assertEquals(3, $result->getItemCount()); // 2 + 1
    }

    public function test_calculates_item_price_successfully(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100.00]);
        $item = CartItem::factory()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);
        $item->setRelation('productVariant', $variant);
        $item->setRelation('cart', Cart::factory()->create(['user_id' => $user->id]));

        $basePrice = new Price(100.00);
        $finalPrice = new Price(85.00);
        $discount = new Discount(15.00, 'B2B discount');
        $expectedResult = new PriceResult($basePrice, $finalPrice, $discount, CustomerType::B2B);

        $this->mockPricingService->shouldReceive('calculatePrice')
            ->once()
            ->with($variant, 2, $user)
            ->andReturn($expectedResult);

        // Act
        $result = $this->priceCoordinator->calculateItemPrice($item, $user);

        // Assert
        $this->assertInstanceOf(PriceResult::class, $result);
        $this->assertEquals(85.00, $result->getFinalPrice()->getAmount());
        $this->assertEquals(15.00, $result->getDiscount()->getAmount());
    }

    public function test_refreshes_all_prices_in_cart(): void
    {
        // Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100.00]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);
        $item->setRelation('productVariant', $variant);

        // Mock customer type detection
        $this->mockCustomerTypeDetector->shouldReceive('detect')
            ->once()
            ->with($user)
            ->andReturn(CustomerType::B2C);

        // Mock pricing calculation
        $basePrice = new Price(100.00);
        $finalPrice = new Price(95.00);
        $discount = new Discount(5.00, 'Refresh discount');
        $priceResult = new PriceResult($basePrice, $finalPrice, $discount, CustomerType::B2C);

        $this->mockPricingService->shouldReceive('calculatePrice')
            ->twice() // Once for refresh, once for update
            ->andReturn($priceResult);

        // Act
        $this->priceCoordinator->refreshAllPrices($cart);

        // Assert
        $item = $item->fresh();
        $this->assertEquals(100.00, $item->base_price);
        $this->assertEquals(95.00, $item->calculated_price);
        $this->assertEquals(5.00, $item->unit_discount);
        $this->assertNotNull($item->price_calculated_at);
    }

    public function test_handles_cart_with_coupon_discount(): void
    {
        // Arrange
        $cart = Cart::factory()->create([
            'coupon_code' => 'TEST10',
            'coupon_discount' => 15.00
        ]);
        
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'calculated_price' => 100.00,
            'total_discount' => 0
        ]);

        $cart->setRelation('items', collect([$item]));
        $product->name = 'Test Product';
        $item->setRelation('product', $product);

        // Act
        $result = $this->priceCoordinator->calculateCartSummary($cart);

        // Assert
        $this->assertEquals(100.00, $result->getSubtotal());
        $this->assertEquals(15.00, $result->getDiscount()); // Coupon discount
        $this->assertEquals(85.00, $result->getTotal());
        $this->assertTrue($result->hasDiscount());
        
        $appliedDiscounts = $result->getAppliedDiscounts();
        $this->assertCount(1, $appliedDiscounts);
        $this->assertEquals('coupon', $appliedDiscounts[0]['type']);
        $this->assertEquals('TEST10', $appliedDiscounts[0]['code']);
    }

    public function test_handles_empty_cart(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $cart->setRelation('items', collect([]));

        // Act
        $result = $this->priceCoordinator->calculateCartSummary($cart);

        // Assert
        $this->assertEquals(0.00, $result->getSubtotal());
        $this->assertEquals(0.00, $result->getDiscount());
        $this->assertEquals(0.00, $result->getTotal());
        $this->assertEquals(0, $result->getItemCount());
        $this->assertTrue($result->isEmpty());
        $this->assertFalse($result->hasDiscount());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}