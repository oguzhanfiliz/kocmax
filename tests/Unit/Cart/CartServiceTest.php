<?php

declare(strict_types=1);

namespace Tests\Unit\Cart;

use App\Services\Cart\CartService;
use App\Services\Cart\CartValidationService;
use App\Services\Cart\CartPriceCoordinator;
use App\Services\Cart\AuthenticatedCartStrategy;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\User;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Cart\CheckoutContext;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\CheckoutValidationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;
    private $mockStrategy;
    private $mockValidator;
    private $mockPriceCoordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockStrategy = Mockery::mock(AuthenticatedCartStrategy::class);
        $this->mockValidator = Mockery::mock(CartValidationService::class);
        $this->mockPriceCoordinator = Mockery::mock(CartPriceCoordinator::class);

        $this->cartService = new CartService(
            $this->mockStrategy,
            $this->mockValidator,
            $this->mockPriceCoordinator
        );
    }

    public function test_adds_item_to_cart_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10,
            'price' => 100.00
        ]);
        $quantity = 2;

        $validationResult = new \App\ValueObjects\Cart\CartValidationResult(true, []);
        $this->mockValidator->shouldReceive('validateAddItem')
            ->once()
            ->with($cart, $variant, $quantity)
            ->andReturn($validationResult);

        $this->mockStrategy->shouldReceive('addItem')
            ->once()
            ->with($cart, $variant, $quantity);

        $mockSummary = Mockery::mock(CartSummary::class);
        $this->mockPriceCoordinator->shouldReceive('updateCartPricing')
            ->once()
            ->with($cart)
            ->andReturn($mockSummary);

        // Act
        $this->cartService->addItem($cart, $variant, $quantity);

        // Assert
        $this->assertTrue(true); // If no exception is thrown, test passes
    }

    public function test_throws_validation_exception_when_adding_invalid_item(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 1,
            'price' => 100.00
        ]);
        $quantity = 5; // More than available stock

        $validationResult = new \App\ValueObjects\Cart\CartValidationResult(false, ['Insufficient stock']);
        $this->mockValidator->shouldReceive('validateAddItem')
            ->once()
            ->with($cart, $variant, $quantity)
            ->andReturn($validationResult);

        // Act & Assert
        $this->expectException(CartValidationException::class);
        $this->cartService->addItem($cart, $variant, $quantity);
    }

    public function test_updates_item_quantity_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $item = CartItem::factory()->create(['cart_id' => $cart->id, 'quantity' => 1]);
        $newQuantity = 3;

        $validationResult = new \App\ValueObjects\Cart\CartValidationResult(true, []);
        $this->mockValidator->shouldReceive('validateQuantityUpdate')
            ->once()
            ->with($item, $newQuantity)
            ->andReturn($validationResult);

        $this->mockStrategy->shouldReceive('updateQuantity')
            ->once()
            ->with($cart, $item, $newQuantity);

        $mockSummary = Mockery::mock(CartSummary::class);
        $this->mockPriceCoordinator->shouldReceive('updateCartPricing')
            ->once()
            ->with($cart)
            ->andReturn($mockSummary);

        // Act
        $this->cartService->updateQuantity($cart, $item, $newQuantity);

        // Assert
        $this->assertTrue(true); // If no exception is thrown, test passes
    }

    public function test_removes_item_from_cart_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->mockStrategy->shouldReceive('removeItem')
            ->once()
            ->with($cart, $item);

        $mockSummary = Mockery::mock(CartSummary::class);
        $this->mockPriceCoordinator->shouldReceive('updateCartPricing')
            ->once()
            ->with($cart)
            ->andReturn($mockSummary);

        // Act
        $this->cartService->removeItem($cart, $item);

        // Assert
        $this->assertTrue(true); // If no exception is thrown, test passes
    }

    public function test_clears_cart_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

        $this->mockStrategy->shouldReceive('clear')
            ->once()
            ->with($cart);

        // Act
        $this->cartService->clearCart($cart);

        // Assert
        $this->assertTrue(true); // If no exception is thrown, test passes
    }

    public function test_calculates_cart_summary(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $expectedSummary = new CartSummary(100.0, 10.0, 90.0, 2);

        $this->mockPriceCoordinator->shouldReceive('calculateCartSummary')
            ->once()
            ->with($cart)
            ->andReturn($expectedSummary);

        // Act
        $result = $this->cartService->calculateSummary($cart);

        // Assert
        $this->assertInstanceOf(CartSummary::class, $result);
        $this->assertEquals(90.0, $result->getTotal());
        $this->assertEquals(2, $result->getItemCount());
    }

    public function test_prepares_checkout_context_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create(['customer_type' => 'b2c']);
        $items = CartItem::factory()->count(2)->create(['cart_id' => $cart->id]);
        $cart->setRelation('items', $items);

        $validationResult = new \App\ValueObjects\Cart\CartValidationResult(true, []);
        $this->mockValidator->shouldReceive('validateForCheckout')
            ->once()
            ->with($cart)
            ->andReturn($validationResult);

        $expectedSummary = new CartSummary(100.0, 10.0, 90.0, 2);
        $this->mockPriceCoordinator->shouldReceive('calculateCartSummary')
            ->once()
            ->with($cart)
            ->andReturn($expectedSummary);

        // Act
        $result = $this->cartService->prepareCheckout($cart);

        // Assert
        $this->assertInstanceOf(CheckoutContext::class, $result);
        $this->assertEquals($cart->id, $result->getCartId());
        $this->assertEquals(90.0, $result->getTotalAmount());
        $this->assertEquals('b2c', $result->getCustomerType());
    }

    public function test_throws_checkout_validation_exception_for_invalid_cart(): void
    {
        // Arrange
        $cart = Cart::factory()->create();

        $validationResult = new \App\ValueObjects\Cart\CartValidationResult(false, ['Cart is empty']);
        $this->mockValidator->shouldReceive('validateForCheckout')
            ->once()
            ->with($cart)
            ->andReturn($validationResult);

        // Act & Assert
        $this->expectException(CheckoutValidationException::class);
        $this->cartService->prepareCheckout($cart);
    }

    public function test_migrates_guest_cart_to_user_cart(): void
    {
        // Arrange
        $sessionId = 'test-session-id';
        $user = User::factory()->create();
        $guestCart = Cart::factory()->create([
            'session_id' => $sessionId,
            'user_id' => null
        ]);
        CartItem::factory()->count(2)->create(['cart_id' => $guestCart->id]);

        $mockSummary = Mockery::mock(CartSummary::class);
        $this->mockPriceCoordinator->shouldReceive('updateCartPricing')
            ->once()
            ->andReturn($mockSummary);

        // Act
        $result = $this->cartService->migrateGuestCart($sessionId, $user);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertNull($result->session_id);
    }

    public function test_returns_null_when_no_guest_cart_to_migrate(): void
    {
        // Arrange
        $sessionId = 'non-existent-session';
        $user = User::factory()->create();

        // Act
        $result = $this->cartService->migrateGuestCart($sessionId, $user);

        // Assert
        $this->assertNull($result);
    }

    public function test_merges_guest_cart_into_existing_user_cart(): void
    {
        // Arrange
        $sessionId = 'test-session-id';
        $user = User::factory()->create();
        
        $guestCart = Cart::factory()->create([
            'session_id' => $sessionId,
            'user_id' => null
        ]);
        
        $userCart = Cart::factory()->create(['user_id' => $user->id]);
        
        $product1 = Product::factory()->create();
        $variant1 = ProductVariant::factory()->create(['product_id' => $product1->id]);
        
        $product2 = Product::factory()->create();
        $variant2 = ProductVariant::factory()->create(['product_id' => $product2->id]);

        // Guest cart items
        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_variant_id' => $variant1->id,
            'quantity' => 2
        ]);
        
        CartItem::factory()->create([
            'cart_id' => $guestCart->id,
            'product_variant_id' => $variant2->id,
            'quantity' => 1
        ]);

        // User cart already has one of the same products
        CartItem::factory()->create([
            'cart_id' => $userCart->id,
            'product_variant_id' => $variant1->id,
            'quantity' => 1
        ]);

        $mockSummary = Mockery::mock(CartSummary::class);
        $this->mockPriceCoordinator->shouldReceive('updateCartPricing')
            ->once()
            ->with($userCart)
            ->andReturn($mockSummary);

        // Act
        $result = $this->cartService->migrateGuestCart($sessionId, $user);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($userCart->id, $result->id);
        
        // Verify guest cart is deleted
        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
        
        // Verify items are merged (variant1: 1+2=3, variant2: 0+1=1)
        $mergedItems = $result->fresh()->items;
        $this->assertCount(2, $mergedItems);
        
        $variant1Item = $mergedItems->where('product_variant_id', $variant1->id)->first();
        $this->assertEquals(3, $variant1Item->quantity);
        
        $variant2Item = $mergedItems->where('product_variant_id', $variant2->id)->first();
        $this->assertEquals(1, $variant2Item->quantity);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}