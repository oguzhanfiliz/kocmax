# Cart Domain Architecture

## 📋 Domain Overview

Bu dokümantasyon, B2B-B2C e-ticaret platformunun Cart Domain mimarisini, Domain-Driven Design (DDD) prensipleri doğrultusunda tanımlar.

### Domain Scope
- **İçerir**: Sepet yönetimi, ürün ekleme/çıkarma, fiyat hesaplama koordinasyonu, checkout hazırlığı
- **İçermez**: Sipariş oluşturma, ödeme işlemleri, kargo yönetimi (bunlar Order Domain'in sorumluluğu)

### Domain Language (Ubiquitous Language)
- **Cart**: Müşterinin seçtiği ürünlerin tutulduğu sepet
- **CartItem**: Sepetteki her bir ürün kalemi
- **Guest Cart**: Oturum açmamış kullanıcıların sepeti (session-based)
- **Authenticated Cart**: Kayıtlı kullanıcıların sepeti (database-stored)
- **Cart Strategy**: B2B/B2C/Guest için farklı sepet davranışları
- **Checkout Context**: Order Domain'e aktarılacak sepet verisi

---

## 🏗️ Architectural Patterns

### 1. Domain-Driven Design (DDD)
```
Cart Domain (Bounded Context)
├── Domain Services     # CartService, CartValidationService
├── Domain Objects      # Cart, CartItem (Aggregates)
├── Value Objects       # CartSummary, CheckoutContext
├── Domain Events       # CartItemAdded, CartCleared
└── Repositories        # CartRepository (interface)
```

### 2. Strategy Pattern
```php
interface CartStrategyInterface
{
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void;
    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void;
    public function removeItem(Cart $cart, CartItem $item): void;
    public function calculateTotals(Cart $cart): CartSummary;
}

// Implementations
├── GuestCartStrategy.php        # Session-based cart logic
├── AuthenticatedCartStrategy.php # Database cart logic
└── B2BCartStrategy.php          # B2B specific features (credit limits, etc.)
```

### 3. Command Pattern
```php
interface CartCommandInterface
{
    public function execute(): mixed;
}

// Commands
├── AddItemCommand.php           # Add product to cart
├── UpdateQuantityCommand.php    # Update item quantity
├── RemoveItemCommand.php        # Remove item from cart
├── ApplyCouponCommand.php       # Apply discount coupon
└── ClearCartCommand.php         # Clear entire cart
```

### 4. Service Layer Pattern
```php
class CartService // Domain Service
{
    public function __construct(
        private CartStrategyInterface $strategy,
        private CartValidationService $validator,
        private CartPricingService $pricingService
    ) {}

    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        // 1. Validate business rules
        $validation = $this->validator->validateAddItem($cart, $variant, $quantity);
        if (!$validation->isValid()) {
            throw new CartValidationException($validation->getErrors());
        }

        // 2. Execute via strategy
        $this->strategy->addItem($cart, $variant, $quantity);

        // 3. Recalculate totals
        $this->recalculateTotals($cart);

        // 4. Emit domain event
        event(new CartItemAdded($cart, $variant, $quantity));
    }
}
```

---

## 🎯 Core Components

### 1. Domain Services

#### CartService (Main Domain Service)
```php
<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartServiceInterface;
use App\Contracts\Cart\CartStrategyInterface;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Cart\CheckoutContext;

class CartService implements CartServiceInterface
{
    public function __construct(
        private CartStrategyInterface $strategy,
        private CartValidationService $validator,
        private CartPricingService $pricingService
    ) {}

    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $this->validateOperation($cart, 'add_item', compact('variant', 'quantity'));
        $this->strategy->addItem($cart, $variant, $quantity);
        $this->recalculateTotals($cart);
    }

    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void
    {
        $this->validateOperation($cart, 'update_quantity', compact('item', 'quantity'));
        $this->strategy->updateQuantity($cart, $item, $quantity);
        $this->recalculateTotals($cart);
    }

    public function prepareCheckout(Cart $cart): CheckoutContext
    {
        $validation = $this->validator->validateForCheckout($cart);
        if (!$validation->isValid()) {
            throw new CheckoutValidationException($validation->getErrors());
        }

        $summary = $this->calculateFinalSummary($cart);
        
        return new CheckoutContext(
            cartId: $cart->id,
            items: $cart->items->toArray(),
            summary: $summary,
            customerType: $this->detectCustomerType($cart->user),
            metadata: $this->prepareCheckoutMetadata($cart)
        );
    }
}
```

#### CartValidationService (Business Rules)
```php
<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\ValueObjects\Cart\CartValidationResult;

class CartValidationService
{
    public function validateAddItem(Cart $cart, ProductVariant $variant, int $quantity): CartValidationResult
    {
        $errors = [];

        // Stock validation
        if ($variant->stock < $quantity) {
            $errors[] = "Insufficient stock. Available: {$variant->stock}, Requested: {$quantity}";
        }

        // Quantity limits
        if ($quantity <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }

        if ($quantity > 999) {
            $errors[] = "Maximum quantity per item is 999";
        }

        // Product availability
        if (!$variant->product->is_active) {
            $errors[] = "Product is not available";
        }

        // B2B specific validations
        if ($cart->user && $cart->user->isDealer()) {
            $minimumOrderAmount = $cart->user->pricingTier?->min_order_amount ?? 0;
            // Additional B2B validations...
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }

    public function validateForCheckout(Cart $cart): CartValidationResult
    {
        $errors = [];

        // Empty cart check
        if ($cart->items->isEmpty()) {
            $errors[] = "Cart is empty";
        }

        // Item validations
        foreach ($cart->items as $item) {
            $itemValidation = $this->validateCartItem($item);
            if (!$itemValidation->isValid()) {
                $errors = array_merge($errors, $itemValidation->getErrors());
            }
        }

        // Credit limit for B2B
        if ($cart->user && $cart->user->isDealer()) {
            $creditValidation = $this->validateCreditLimit($cart);
            if (!$creditValidation->isValid()) {
                $errors = array_merge($errors, $creditValidation->getErrors());
            }
        }

        return new CartValidationResult(
            isValid: empty($errors),
            errors: $errors
        );
    }
}
```

#### CartPricingService (Pricing Coordination)
```php
<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Services\PricingService;
use App\Services\Campaign\CampaignEngine;
use App\ValueObjects\Cart\CartSummary;

class CartPricingService
{
    public function __construct(
        private PricingService $pricingService,
        private CampaignEngine $campaignEngine
    ) {}

    public function calculateCartSummary(Cart $cart): CartSummary
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $itemDetails = [];

        // Calculate each item with real-time pricing
        foreach ($cart->items as $item) {
            $priceResult = $this->pricingService->calculatePrice(
                $item->productVariant,
                $item->quantity,
                $cart->user
            );

            $itemSubtotal = $priceResult->getFinalPrice()->getAmount() * $item->quantity;
            $itemDiscount = ($priceResult->getBasePrice()->getAmount() - $priceResult->getFinalPrice()->getAmount()) * $item->quantity;

            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;

            $itemDetails[] = [
                'item_id' => $item->id,
                'base_price' => $priceResult->getBasePrice()->getAmount(),
                'final_price' => $priceResult->getFinalPrice()->getAmount(),
                'discount' => $priceResult->getDiscount()?->getAmount() ?? 0,
                'subtotal' => $itemSubtotal
            ];
        }

        // Apply cart-level campaigns
        $cartContext = $this->buildCartContext($cart, $subtotal);
        $applicableCampaigns = $this->campaignEngine->getApplicableCampaigns($cartContext);
        
        $campaignDiscount = 0;
        foreach ($applicableCampaigns as $campaign) {
            $campaignDiscount += $campaign->calculateDiscount($cartContext);
        }

        return new CartSummary(
            subtotal: $subtotal,
            discount: $totalDiscount + $campaignDiscount,
            total: $subtotal - $totalDiscount - $campaignDiscount,
            itemCount: $cart->items->sum('quantity'),
            itemDetails: $itemDetails,
            appliedCampaigns: $applicableCampaigns->toArray()
        );
    }
}
```

### 2. Strategy Implementations

#### GuestCartStrategy (Session-based)
```php
<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use Illuminate\Support\Facades\Session;

class GuestCartStrategy implements CartStrategyInterface
{
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $sessionKey = "guest_cart_{$cart->session_id}";
        $cartData = Session::get($sessionKey, ['items' => []]);

        $existingItemKey = $this->findExistingItem($cartData['items'], $variant->id);
        
        if ($existingItemKey !== null) {
            $cartData['items'][$existingItemKey]['quantity'] += $quantity;
        } else {
            $cartData['items'][] = [
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'added_at' => now()->toISOString()
            ];
        }

        Session::put($sessionKey, $cartData);
        
        // Sync with database model
        $this->syncCartFromSession($cart);
    }

    public function removeItem(Cart $cart, CartItem $item): void
    {
        $sessionKey = "guest_cart_{$cart->session_id}";
        $cartData = Session::get($sessionKey, ['items' => []]);

        $cartData['items'] = array_filter($cartData['items'], function ($sessionItem) use ($item) {
            return $sessionItem['product_variant_id'] !== $item->product_variant_id;
        });

        Session::put($sessionKey, $cartData);
        $this->syncCartFromSession($cart);
    }

    private function syncCartFromSession(Cart $cart): void
    {
        $sessionKey = "guest_cart_{$cart->session_id}";
        $cartData = Session::get($sessionKey, ['items' => []]);

        // Clear existing cart items
        $cart->items()->delete();

        // Recreate from session
        foreach ($cartData['items'] as $sessionItem) {
            $cart->items()->create([
                'product_id' => $sessionItem['product_id'],
                'product_variant_id' => $sessionItem['product_variant_id'],
                'quantity' => $sessionItem['quantity']
            ]);
        }

        $cart->refresh();
    }
}
```

#### AuthenticatedCartStrategy (Database-based)
```php
<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;

class AuthenticatedCartStrategy implements CartStrategyInterface
{
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void
    {
        $existingItem = $cart->items()
            ->where('product_variant_id', $variant->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price, // Will be recalculated by PricingService
                'discounted_price' => null
            ]);
        }

        $cart->touch(); // Update cart timestamp
    }

    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($cart, $item);
            return;
        }

        $item->update(['quantity' => $quantity]);
        $cart->touch();
    }

    public function removeItem(Cart $cart, CartItem $item): void
    {
        $item->delete();
        $cart->touch();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update([
            'total_amount' => 0,
            'discounted_amount' => 0,
            'coupon_code' => null,
            'coupon_discount' => 0,
        ]);
    }
}
```

### 3. Value Objects

#### CartSummary
```php
<?php

declare(strict_types=1);

namespace App\ValueObjects\Cart;

class CartSummary
{
    public function __construct(
        private readonly float $subtotal,
        private readonly float $discount,
        private readonly float $total,
        private readonly int $itemCount,
        private readonly array $itemDetails = [],
        private readonly array $appliedCampaigns = []
    ) {}

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    public function getItemDetails(): array
    {
        return $this->itemDetails;
    }

    public function getAppliedCampaigns(): array
    {
        return $this->appliedCampaigns;
    }

    public function isEmpty(): bool
    {
        return $this->itemCount === 0;
    }

    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'item_count' => $this->itemCount,
            'item_details' => $this->itemDetails,
            'applied_campaigns' => $this->appliedCampaigns,
            'is_empty' => $this->isEmpty(),
            'has_discount' => $this->hasDiscount()
        ];
    }
}
```

#### CheckoutContext (Data Transfer to Order Domain)
```php
<?php

declare(strict_types=1);

namespace App\ValueObjects\Cart;

class CheckoutContext
{
    public function __construct(
        private readonly int $cartId,
        private readonly array $items,
        private readonly CartSummary $summary,
        private readonly string $customerType,
        private readonly array $metadata = []
    ) {}

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSummary(): CartSummary
    {
        return $this->summary;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getTotalAmount(): float
    {
        return $this->summary->getTotal();
    }

    public function getItemCount(): int
    {
        return $this->summary->getItemCount();
    }

    public function toArray(): array
    {
        return [
            'cart_id' => $this->cartId,
            'items' => $this->items,
            'summary' => $this->summary->toArray(),
            'customer_type' => $this->customerType,
            'metadata' => $this->metadata
        ];
    }
}
```

---

## 🔗 Domain Integration

### Integration with Pricing Domain
```php
// Cart domain coordinates with Pricing domain
class CartPricingService
{
    public function updateItemPrices(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $priceResult = $this->pricingService->calculatePrice(
                $item->productVariant,
                $item->quantity,
                $cart->user
            );

            $item->update([
                'price' => $priceResult->getBasePrice()->getAmount(),
                'discounted_price' => $priceResult->getFinalPrice()->getAmount()
            ]);
        }
    }
}
```

### Integration with Campaign Domain
```php
// Cart domain uses Campaign domain for cart-level discounts
class CartService
{
    public function applyCampaigns(Cart $cart): void
    {
        $cartContext = new \App\ValueObjects\Campaign\CartContext(
            items: $cart->items,
            totalAmount: $cart->total_amount,
            customerType: $this->customerTypeDetector->detect($cart->user)
        );

        $campaigns = $this->campaignEngine->getApplicableCampaigns($cartContext);
        
        // Apply cart-level discounts
        $totalCampaignDiscount = 0;
        foreach ($campaigns as $campaign) {
            $totalCampaignDiscount += $campaign->calculateDiscount($cartContext);
        }

        $cart->update([
            'campaign_discount' => $totalCampaignDiscount,
            'discounted_amount' => $cart->total_amount - $totalCampaignDiscount
        ]);
    }
}
```

### Handoff to Order Domain
```php
// Clean domain boundary
class CheckoutCoordinator // Application Service
{
    public function processCheckout(Cart $cart, array $checkoutData): Order
    {
        // 1. Cart domain prepares checkout context
        $checkoutContext = $this->cartService->prepareCheckout($cart);
        
        // 2. Order domain creates order from context
        $order = $this->orderService->createFromCheckout($checkoutContext, $checkoutData);
        
        // 3. Cart domain clears after successful order
        if ($order->isPending() || $order->isPaid()) {
            $this->cartService->clearAfterCheckout($cart);
        }
        
        return $order;
    }
}
```

---

## 📊 Performance Considerations

### Caching Strategy
```php
class CartService
{
    public function calculateSummary(Cart $cart): CartSummary
    {
        $cacheKey = "cart_summary_{$cart->id}_{$cart->updated_at->timestamp}";
        
        return Cache::remember($cacheKey, 300, function () use ($cart) {
            return $this->cartPricingService->calculateCartSummary($cart);
        });
    }
}
```

### Database Optimization
```php
// Eager loading for cart operations
$cart = Cart::with([
    'items.product',
    'items.productVariant',
    'user.pricingTier'
])->find($cartId);
```

### Session Optimization
```php
class GuestCartStrategy
{
    public function optimizeSession(): void
    {
        // Compress session data for large carts
        $cartData = Session::get($sessionKey);
        if (count($cartData['items']) > 20) {
            $compressed = gzcompress(serialize($cartData));
            Session::put($sessionKey . '_compressed', $compressed);
            Session::forget($sessionKey);
        }
    }
}
```

---

## 🧪 Testing Strategy

### Unit Testing
```php
class CartServiceTest extends TestCase
{
    public function test_adds_item_to_cart_successfully(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $variant = ProductVariant::factory()->create(['stock' => 10]);
        
        // Act
        $this->cartService->addItem($cart, $variant, 2);
        
        // Assert
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2
        ]);
    }

    public function test_validates_stock_before_adding_item(): void
    {
        // Arrange
        $cart = Cart::factory()->create();
        $variant = ProductVariant::factory()->create(['stock' => 1]);
        
        // Act & Assert
        $this->expectException(CartValidationException::class);
        $this->cartService->addItem($cart, $variant, 5);
    }
}
```

### Integration Testing
```php
class CartPricingIntegrationTest extends TestCase
{
    public function test_cart_integrates_with_pricing_service(): void
    {
        // Arrange
        $user = User::factory()->create(['is_approved_dealer' => true]);
        $tier = CustomerPricingTier::factory()->create(['discount_percentage' => 10]);
        $user->pricingTier()->associate($tier);
        
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $variant = ProductVariant::factory()->create(['price' => 100]);
        
        // Act
        $this->cartService->addItem($cart, $variant, 1);
        $summary = $this->cartService->calculateSummary($cart);
        
        // Assert
        $this->assertEquals(90.0, $summary->getTotal()); // 10% discount applied
    }
}
```

---

## 🎉 Sonuç

Bu Cart Domain Architecture, Domain-Driven Design prensiplerini takip ederek:

1. **Clear Domain Boundaries**: Cart sorumluluklarını net tanımlar
2. **Strategy Pattern**: Farklı kullanıcı tiplerini destekler
3. **Clean Integration**: Diğer domain'lerle temiz entegrasyon
4. **Scalable Design**: Büyüyen iş gereksinimlerine uyum
5. **Testable Architecture**: Comprehensive test coverage

Bu mimari, mevcut pattern'lerinizle uyumlu ve maintainable bir Cart Domain sistemi sağlar.