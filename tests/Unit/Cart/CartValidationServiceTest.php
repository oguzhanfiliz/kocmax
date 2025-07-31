<?php

declare(strict_types=1);

namespace Tests\Unit\Cart;

use App\Services\Cart\CartValidationService;
use App\Services\Pricing\CustomerTypeDetector;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\CustomerPricingTier;
use App\ValueObjects\Cart\CartValidationResult;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CartValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartValidationService $validationService;
    private $mockCustomerTypeDetector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockCustomerTypeDetector = Mockery::mock(CustomerTypeDetector::class);
        $this->validationService = new CartValidationService($this->mockCustomerTypeDetector);
    }

    public function test_validates_add_item_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $quantity = 5;

        // Act
        $result = $this->validationService->validateAddItem($cart, $variant, $quantity);

        // Assert
        $this->assertInstanceOf(CartValidationResult::class, $result);
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_fails_validation_for_insufficient_stock(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 2
        ]);
        $quantity = 5;

        // Act
        $result = $this->validationService->validateAddItem($cart, $variant, $quantity);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Insufficient stock. Available: 2, Requested: 5', $result->getErrors());
    }

    public function test_fails_validation_for_inactive_product(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => false]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $quantity = 1;

        // Act
        $result = $this->validationService->validateAddItem($cart, $variant, $quantity);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Product is not available', $result->getErrors());
    }

    public function test_fails_validation_for_zero_quantity(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $quantity = 0;

        // Act
        $result = $this->validationService->validateAddItem($cart, $variant, $quantity);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Quantity must be greater than 0', $result->getErrors());
    }

    public function test_fails_validation_for_excessive_quantity(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 1000
        ]);
        $quantity = 1001;

        // Act
        $result = $this->validationService->validateAddItem($cart, $variant, $quantity);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Maximum quantity per item is 999', $result->getErrors());
    }

    public function test_validates_quantity_update_successfully(): void
    {
        // Arrange
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);
        $newQuantity = 5;

        // Act
        $result = $this->validationService->validateQuantityUpdate($item, $newQuantity);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_fails_quantity_update_for_negative_quantity(): void
    {
        // Arrange
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);
        $newQuantity = -1;

        // Act
        $result = $this->validationService->validateQuantityUpdate($item, $newQuantity);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Quantity cannot be negative', $result->getErrors());
    }

    public function test_validates_checkout_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $cart->setRelation('items', collect([$item]));

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_fails_checkout_validation_for_empty_cart(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $cart->setRelation('items', collect([]));

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Cart is empty', $result->getErrors());
    }

    public function test_validates_b2b_checkout_with_sufficient_credit(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'credit_limit' => 1000.00,
            'current_debt' => 200.00
        ]);
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 500.00
        ]);
        
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertTrue($result->isValid());
        $this->assertEmpty($result->getErrors());
    }

    public function test_fails_b2b_checkout_with_insufficient_credit(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_approved_dealer' => true,
            'credit_limit' => 500.00,
            'current_debt' => 400.00
        ]);
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 200.00
        ]);
        
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertTrue(str_contains(implode(' ', $result->getErrors()), 'Insufficient credit limit'));
    }

    public function test_fails_b2b_checkout_for_unapproved_dealer(): void
    {
        // Arrange
        $user = User::factory()->create([
            'is_approved_dealer' => false
        ]);
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 100.00
        ]);
        
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Dealer account is not approved for checkout', $result->getErrors());
    }

    public function test_validates_minimum_order_amount(): void
    {
        // Arrange
        $tier = CustomerPricingTier::factory()->create(['min_order_amount' => 100.00]);
        $user = User::factory()->create();
        $user->pricingTier()->associate($tier);
        
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 50.00
        ]);
        
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 10
        ]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1
        ]);

        $cart->setRelation('items', collect([$item]));
        $cart->setRelation('user', $user);

        // Act
        $result = $this->validationService->validateForCheckout($cart);

        // Assert
        $this->assertFalse($result->isValid());
        $this->assertContains('Minimum order amount is 100 TL', $result->getErrors());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}