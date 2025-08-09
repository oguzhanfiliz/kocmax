<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Cart\CartService;
use App\Services\Cart\CartStrategyFactory;
use App\Services\MultiCurrencyPricingService;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateQuantityRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Sepet",
 *     description="Alışveriş sepeti yönetimi API uç noktaları"
 * )
 */
class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CartStrategyFactory $strategyFactory,
        private MultiCurrencyPricingService $multiCurrencyPricingService
    ) {}

    /**
     * @OA\Get(
     *      path="/api/v1/cart",
     *      operationId="getCart",
     *      tags={"Cart"},
     *      summary="Mevcut kullanıcının sepetini al",
     *      description="Kimliği doğrulanmış veya misafir kullanıcılar için para birimi dönüştürme ile mevcut sepeti alın",
     *      @OA\Parameter(
     *          name="currency",
     *          description="Target currency code (e.g., TRY, USD, EUR)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", example="USD")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Cart retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="cart", type="object"),
     *                  @OA\Property(property="summary", type="object")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Failed to retrieve cart",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Failed to retrieve cart")
     *          )
     *      )
     * )
     * 
     * Get current user's cart
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $targetCurrency = $request->get('_currency', $request->get('currency', 'TRY'));
            
            // Calculate summary with currency conversion
            $summary = $this->cartService->calculateSummary($cart, $targetCurrency);

            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => new CartResource($cart->load(['items.productVariant']), $targetCurrency),
                    'summary' => new CartSummaryResource($summary),
                    'currency' => $targetCurrency,
                    'available_currencies' => $this->multiCurrencyPricingService->getAvailableCurrencies()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get cart', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/cart/items",
     *      operationId="addItemToCart",
     *      tags={"Cart"},
     *      summary="Sepete ürün ekle",
     *      description="Kullanıcının sepetine bir ürün varyantı ekleyin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"product_variant_id"},
     *              @OA\Property(property="product_variant_id", type="integer", example=1),
     *              @OA\Property(property="quantity", type="integer", example=2, minimum=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Item added to cart successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Item added to cart successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to add item to cart",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Failed to add item to cart")
     *          )
     *      )
     * )
     * 
     * Add item to cart
     */
    public function addItem(AddItemRequest $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $variant = ProductVariant::findOrFail($request->product_variant_id);
            $quantity = $request->quantity ?? 1;

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->addItem($cart, $variant, $quantity);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to add item to cart', [
                'error' => $e->getMessage(),
                'variant_id' => $request->product_variant_id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/cart/items/{item}",
     *      operationId="updateCartItem",
     *      tags={"Cart"},
     *      summary="Sepetteki ürün miktarını güncelle",
     *      description="Mevcut bir sepet öğesinin miktarını güncelleyin",
     *      @OA\Parameter(
     *          name="item",
     *          description="Cart item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"quantity"},
     *              @OA\Property(property="quantity", type="integer", example=3, minimum=1)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Cart item updated successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to update cart item"
     *      )
     * )
     * 
     * Update item quantity in cart
     */
    public function updateItem(UpdateQuantityRequest $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $item = $cart->items()->findOrFail($itemId);
            $quantity = $request->quantity;

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->updateQuantity($cart, $item, $quantity);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update cart item', [
                'error' => $e->getMessage(),
                'item_id' => $itemId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/cart/items/{item}",
     *      operationId="removeCartItem",
     *      tags={"Cart"},
     *      summary="Ürünü sepetten kaldır",
     *      description="Kullanıcının sepetinden bir ürünü kaldırın",
     *      @OA\Parameter(
     *          name="item",
     *          description="Cart item ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Item removed from cart successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to remove cart item"
     *      )
     * )
     * 
     * Remove item from cart
     */
    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $item = $cart->items()->findOrFail($itemId);

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->removeItem($cart, $item);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove cart item', [
                'error' => $e->getMessage(),
                'item_id' => $itemId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove cart item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/cart",
     *      operationId="clearCart",
     *      tags={"Cart"},
     *      summary="Tüm sepeti temizle",
     *      description="Kullanıcının sepetindeki tüm ürünleri kaldırın",
     *      @OA\Response(
     *          response=200,
     *          description="Cart cleared successfully"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Failed to clear cart"
     *      )
     * )
     * 
     * Clear entire cart
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);

            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->clearCart($cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items'])),
                    'summary' => new CartSummaryResource($this->cartService->calculateSummary($cart))
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear cart', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/cart/summary",
     *      operationId="getCartSummary",
     *      tags={"Cart"},
     *      summary="Sepet özetini al",
     *      description="Fiyatlandırma bilgileriyle sepet özetini alın",
     *      @OA\Response(
     *          response=200,
     *          description="Cart summary retrieved successfully"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Failed to retrieve cart summary"
     *      )
     * )
     * 
     * Get cart summary only
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'data' => new CartSummaryResource($summary)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get cart summary', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh cart pricing
     */
    public function refreshPricing(Request $request): JsonResponse
    {
        try {
            $cart = $this->getOrCreateCart($request);
            
            // Set appropriate strategy
            $strategy = $this->strategyFactory->create($cart);
            $this->cartService->setStrategy($strategy);

            $this->cartService->refreshPricing($cart);
            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart pricing refreshed successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to refresh cart pricing', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh cart pricing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/cart/migrate",
     *      operationId="migrateGuestCart",
     *      tags={"Cart"},
     *      summary="Misafir sepetini kimliği doğrulanmış kullanıcıya taşı",
     *      description="Ürünleri misafir sepetinden kimliği doğrulanmış kullanıcı sepetine aktarın",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Guest cart migrated successfully"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="User must be authenticated to migrate cart"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Failed to migrate guest cart"
     *      )
     * )
     * 
     * Migrate guest cart to authenticated user
     */
    public function migrate(Request $request): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must be authenticated to migrate cart'
                ], 401);
            }

            $sessionId = $request->session()->getId();
            $user = Auth::user();

            $cart = $this->cartService->migrateGuestCart($sessionId, $user);

            if (!$cart) {
                return response()->json([
                    'success' => true,
                    'message' => 'No guest cart found to migrate',
                    'data' => null
                ]);
            }

            $summary = $this->cartService->calculateSummary($cart);

            return response()->json([
                'success' => true,
                'message' => 'Guest cart migrated successfully',
                'data' => [
                    'cart' => new CartResource($cart->fresh(['items.product', 'items.productVariant'])),
                    'summary' => new CartSummaryResource($summary)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to migrate guest cart', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to migrate guest cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create cart for current request
     */
    private function getOrCreateCart(Request $request): Cart
    {
        if (Auth::check()) {
            // Authenticated user cart
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'total_amount' => 0,
                    'discounted_amount' => 0,
                    'subtotal_amount' => 0,
                ]
            );
        } else {
            // Guest cart
            $sessionId = $request->session()->getId();
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                [
                    'total_amount' => 0,
                    'discounted_amount' => 0,
                    'subtotal_amount' => 0,
                ]
            );
        }
    }
}