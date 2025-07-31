<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartServiceInterface;
use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Cart\CartValidationService;
use App\Services\Cart\CartPriceCoordinator;
use App\ValueObjects\Cart\CartSummary;
use App\ValueObjects\Cart\CheckoutContext;
use App\Events\Cart\CartItemAdded;
use App\Events\Cart\CartItemUpdated;
use App\Events\Cart\CartItemRemoved;
use App\Events\Cart\CartCleared;
use App\Exceptions\Cart\CartValidationException;
use App\Exceptions\Cart\CheckoutValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService implements CartServiceInterface
{
    public function __construct(
        private CartStrategyInterface $strategy,
        private CartValidationService $validator,
        private CartPriceCoordinator $priceCoordinator
    ) {}

    public function addItem(Cart $cart, ProductVariant $variant, int $quantity = 1): void
    {
        try {
            DB::beginTransaction();

            // Validate business rules
            $validation = $this->validator->validateAddItem($cart, $variant, $quantity);
            if (!$validation->isValid()) {
                throw new CartValidationException($validation->getErrors());
            }

            // Execute via strategy
            $this->strategy->addItem($cart, $variant, $quantity);

            // Recalculate prices
            $this->priceCoordinator->updateCartPricing($cart);

            // Emit domain event
            event(new CartItemAdded($cart, $variant, $quantity));

            DB::commit();

            Log::info('Cart item added successfully', [
                'cart_id' => $cart->id,
                'variant_id' => $variant->id,
                'quantity' => $quantity
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add item to cart', [
                'cart_id' => $cart->id,
                'variant_id' => $variant->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void
    {
        try {
            DB::beginTransaction();

            $validation = $this->validator->validateQuantityUpdate($item, $quantity);
            if (!$validation->isValid()) {
                throw new CartValidationException($validation->getErrors());
            }

            $oldQuantity = $item->quantity;
            $this->strategy->updateQuantity($cart, $item, $quantity);
            $this->priceCoordinator->updateCartPricing($cart);

            event(new CartItemUpdated($cart, $item, $oldQuantity, $quantity));

            DB::commit();

            Log::info('Cart item quantity updated', [
                'cart_id' => $cart->id,
                'item_id' => $item->id,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $quantity
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update cart item quantity', [
                'cart_id' => $cart->id,
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function removeItem(Cart $cart, CartItem $item): void
    {
        try {
            DB::beginTransaction();

            $this->strategy->removeItem($cart, $item);
            $this->priceCoordinator->updateCartPricing($cart);

            event(new CartItemRemoved($cart, $item));

            DB::commit();

            Log::info('Cart item removed', [
                'cart_id' => $cart->id,
                'item_id' => $item->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove cart item', [
                'cart_id' => $cart->id,
                'item_id' => $item->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function clearCart(Cart $cart): void
    {
        try {
            DB::beginTransaction();

            $itemCount = $cart->items->count();
            $this->strategy->clear($cart);

            event(new CartCleared($cart, $itemCount));

            DB::commit();

            Log::info('Cart cleared', [
                'cart_id' => $cart->id,
                'items_removed' => $itemCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to clear cart', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calculateSummary(Cart $cart): CartSummary
    {
        return $this->priceCoordinator->calculateCartSummary($cart);
    }

    public function refreshPricing(Cart $cart): void
    {
        $this->priceCoordinator->refreshAllPrices($cart);
    }

    public function prepareCheckout(Cart $cart): CheckoutContext
    {
        $validation = $this->validator->validateForCheckout($cart);
        if (!$validation->isValid()) {
            throw new CheckoutValidationException($validation->getErrors());
        }

        $summary = $this->calculateSummary($cart);
        
        return new CheckoutContext(
            cartId: $cart->id,
            items: $cart->items->toArray(),
            summary: $summary,
            customerType: $cart->customer_type ?? 'guest',
            metadata: $this->prepareCheckoutMetadata($cart)
        );
    }

    public function migrateGuestCart(string $sessionId, User $user): ?Cart
    {
        $guestCart = Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (!$guestCart) {
            return null;
        }

        $userCart = Cart::where('user_id', $user->id)->first();

        if ($userCart) {
            // Merge guest cart into user cart
            return $this->mergeGuestCartIntoUserCart($guestCart, $userCart);
        } else {
            // Convert guest cart to user cart
            $guestCart->update([
                'user_id' => $user->id,
                'session_id' => null
            ]);
            
            $this->priceCoordinator->updateCartPricing($guestCart);
            return $guestCart;
        }
    }

    private function mergeGuestCartIntoUserCart(Cart $guestCart, Cart $userCart): Cart
    {
        try {
            DB::beginTransaction();

            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $existingItem->quantity + $guestItem->quantity
                    ]);
                } else {
                    $userCart->items()->create([
                        'product_id' => $guestItem->product_id,
                        'product_variant_id' => $guestItem->product_variant_id,
                        'quantity' => $guestItem->quantity,
                        'price' => $guestItem->price,
                        'discounted_price' => $guestItem->discounted_price,
                    ]);
                }
            }

            // Update pricing for merged cart
            $this->priceCoordinator->updateCartPricing($userCart);

            // Delete guest cart
            $guestCart->delete();

            DB::commit();

            Log::info('Guest cart merged into user cart', [
                'user_id' => $userCart->user_id,
                'guest_cart_id' => $guestCart->id,
                'user_cart_id' => $userCart->id
            ]);

            return $userCart;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to merge guest cart', [
                'guest_cart_id' => $guestCart->id,
                'user_cart_id' => $userCart->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function prepareCheckoutMetadata(Cart $cart): array
    {
        return [
            'cart_created_at' => $cart->created_at,
            'last_updated_at' => $cart->updated_at,
            'pricing_calculated_at' => $cart->pricing_calculated_at,
            'session_id' => $cart->session_id,
            'item_count' => $cart->items->count(),
            'total_quantity' => $cart->items->sum('quantity'),
        ];
    }
}