<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Cart;
use App\Services\Order\OrderService;
use App\Services\Checkout\CheckoutCoordinator;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Siparişler",
 *     description="Sipariş yönetimi endpointleri"
 * )
 */
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CheckoutCoordinator $checkoutCoordinator
    ) {}

    /**
     * @OA\Get(
     *      path="/api/v1/orders",
     *      operationId="getOrders",
     *      tags={"Orders"},
     *      summary="Kullanıcı siparişlerini al",
     *      description="Kimliği doğrulanmış kullanıcı için isteğe bağlı filtreleme ile siparişleri alın",
     *      security={{ "sanctum": {} }},
     *      @OA\Parameter(
     *          name="status",
     *          description="Filter by order status",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="from_date",
     *          description="Filter orders from date (YYYY-MM-DD)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *      @OA\Parameter(
     *          name="to_date",
     *          description="Filter orders to date (YYYY-MM-DD)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", format="date")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="Items per page (default: 15)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="integer", default=15)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Orders retrieved successfully"
     *      )
     * )
     * 
     * Display a listing of orders for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = Order::with(['items.product', 'items.productVariant'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->get('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->get('to_date'));
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/{order}",
     *      operationId="getOrder",
     *      tags={"Orders"},
     *      summary="Belirli siparişi al",
     *      description="Belirli bir siparişin ayrıntılarını alın",
     *      security={{ "sanctum": {} }},
     *      @OA\Parameter(
     *          name="order",
     *          description="Order ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Order retrieved successfully"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Not authorized to view this order"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Order not found"
     *      )
     * )
     * 
     * Display the specified order
     */
    public function show(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        $order->load(['items.product', 'items.productVariant', 'statusHistory']);

        return response()->json([
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders",
     *      operationId="createOrder",
     *      tags={"Orders"},
     *      summary="Sepetten yeni sipariş oluştur",
     *      description="Ödemeyi işleyin ve kullanıcının sepetinden sipariş oluşturun",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shipping_address", "billing_address"},
     *              @OA\Property(property="shipping_address", type="object"),
     *              @OA\Property(property="billing_address", type="object"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"}),
     *              @OA\Property(property="notes", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Order created successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Cart is empty or validation failed"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Checkout validation failed"
     *      )
     * )
     * 
     * Create a new order from cart (checkout process)
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        // Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json([
                'message' => 'Cart is empty or not found'
            ], 400);
        }

        try {
            // Validate checkout data
            $validation = $this->checkoutCoordinator->validateCheckout($cart, $request->validated());
            
            if (!$validation->isValid()) {
                return response()->json([
                    'message' => 'Checkout validation failed',
                    'errors' => $validation->getErrors(),
                    'warnings' => $validation->getWarnings()
                ], 422);
            }

            // Process checkout
            $order = $this->checkoutCoordinator->processCheckout($cart, $request->validated());
            
            $order->load(['items.product', 'items.productVariant']);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders/guest-checkout",
     *      operationId="guestCheckout",
     *      tags={"Orders"},
     *      summary="Misafir ödemesini işle",
     *      description="Kullanıcı kimlik doğrulaması olmadan sipariş oluşturun",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"cart_data", "shipping_address", "billing_address"},
     *              @OA\Property(property="cart_data", type="object",
     *                  @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *              ),
     *              @OA\Property(property="shipping_address", type="object"),
     *              @OA\Property(property="billing_address", type="object"),
     *              @OA\Property(property="customer_email", type="string", format="email"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"})
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Guest order created successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Cart data is required for guest checkout"
     *      )
     * )
     * 
     * Process guest checkout (no authentication required)
     */
    public function guestCheckout(StoreOrderRequest $request): JsonResponse
    {
        $cartData = $request->input('cart_data', []);
        $checkoutData = $request->except(['cart_data']);

        if (empty($cartData) || empty($cartData['items'])) {
            return response()->json([
                'message' => 'Cart data is required for guest checkout'
            ], 400);
        }

        try {
            $order = $this->checkoutCoordinator->processGuestCheckout($cartData, $checkoutData);
            
            $order->load(['items.product', 'items.productVariant']);

            return response()->json([
                'message' => 'Guest order created successfully',
                'data' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process guest checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order status (admin only)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        Gate::authorize('updateStatus', $order);

        $newStatus = OrderStatus::from($request->input('status'));
        $notes = $request->input('notes');

        try {
            $updated = $this->orderService->updateStatus($order, $newStatus, Auth::user(), $notes);

            if (!$updated) {
                return response()->json([
                    'message' => 'Status transition not allowed'
                ], 400);
            }

            $order->load(['items.product', 'items.productVariant', 'statusHistory']);

            return response()->json([
                'message' => 'Order status updated successfully',
                'data' => new OrderResource($order)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('cancel', $order);

        $reason = $request->input('reason', 'Customer cancellation');

        try {
            $cancelled = $this->orderService->cancelOrder($order, Auth::user(), $reason);

            if (!$cancelled) {
                return response()->json([
                    'message' => 'Order cannot be cancelled at this stage'
                ], 400);
            }

            $order->load(['items.product', 'items.productVariant', 'statusHistory']);

            return response()->json([
                'message' => 'Order cancelled successfully',
                'data' => new OrderResource($order)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for an order
     */
    public function processPayment(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('processPayment', $order);

        $paymentData = $request->validate([
            'payment_method' => 'required|string|in:card,credit,bank_transfer',
            'payment_data' => 'required|array',
            'payment_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            $paymentAmount = $paymentData['payment_amount'] ?? $order->total_amount;
            
            $result = $this->orderService->processPayment(
                $order,
                $paymentData['payment_method'],
                $paymentAmount,
                $paymentData['payment_data']
            );

            if ($result->isSuccess()) {
                $order->load(['items.product', 'items.productVariant']);

                return response()->json([
                    'message' => 'Payment processed successfully',
                    'data' => new OrderResource($order),
                    'payment_result' => [
                        'transaction_id' => $result->getTransactionId(),
                        'amount' => $result->getAmount(),
                        'method' => $result->getMethod()
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => 'Payment processing failed',
                    'error' => $result->getErrorMessage()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment processing error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/{order}/tracking",
     *      operationId="getOrderTracking",
     *      tags={"Orders"},
     *      summary="Sipariş takip bilgilerini al",
     *      description="Sipariş numarasını kullanarak bir siparişin takip ayrıntılarını alın",
     *      @OA\Parameter(
     *          name="order",
     *          description="Order number",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Tracking information retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="order_number", type="string"),
     *                  @OA\Property(property="status", type="string"),
     *                  @OA\Property(property="tracking_number", type="string"),
     *                  @OA\Property(property="shipping_carrier", type="string"),
     *                  @OA\Property(property="estimated_delivery", type="string", format="date-time")
     *              )
     *          )
     *      )
     * )
     * 
     * Get order tracking information
     */
    public function tracking(Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        $trackingInfo = [
            'order_number' => $order->order_number,
            'status' => $order->status->value,
            'status_label' => $order->status->getLabel(),
            'tracking_number' => $order->tracking_number,
            'shipping_carrier' => $order->shipping_carrier,
            'estimated_delivery' => $order->estimated_delivery_at,
            'shipped_at' => $order->shipped_at,
            'delivered_at' => $order->delivered_at,
            'history' => $order->statusHistory->map(function ($history) {
                return [
                    'status' => $history->status,
                    'status_label' => OrderStatus::from($history->status)->getLabel(),
                    'notes' => $history->notes,
                    'changed_by' => $history->user?->name,
                    'changed_at' => $history->created_at
                ];
            })
        ];

        return response()->json([
            'data' => $trackingInfo
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/orders/user/summary",
     *      operationId="getUserOrderSummary",
     *      tags={"Orders"},
     *      summary="Kullanıcı sipariş özetini al",
     *      description="Kimliği doğrulanmış kullanıcı için sipariş istatistiklerini ve özetini alın",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Order summary retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="total_orders", type="integer"),
     *                  @OA\Property(property="total_spent", type="number", format="float"),
     *                  @OA\Property(property="recent_orders", type="array", @OA\Items(type="object")),
     *                  @OA\Property(property="status_counts", type="object")
     *              )
     *          )
     *      )
     * )
     * 
     * Get order summary/statistics for current user
     */
    public function summary(): JsonResponse
    {
        $user = Auth::user();
        
        $summary = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'recent_orders' => Order::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'order_number', 'status', 'total_amount', 'created_at']),
            'status_counts' => Order::where('user_id', $user->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
        ];

        return response()->json([
            'data' => $summary
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/orders/estimate-checkout",
     *      operationId="estimateCheckout",
     *      tags={"Orders"},
     *      summary="Ödeme maliyetlerini tahmin et",
     *      description="Sipariş oluşturmadan önce gönderim ve vergiler dahil ödeme maliyetlerini hesaplayın",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shipping_country", "shipping_city"},
     *              @OA\Property(property="shipping_country", type="string", maxLength=2, example="TR"),
     *              @OA\Property(property="billing_country", type="string", maxLength=2, example="TR"),
     *              @OA\Property(property="shipping_city", type="string", example="Istanbul"),
     *              @OA\Property(property="payment_method", type="string", enum={"card", "credit", "bank_transfer"})
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Checkout estimate calculated successfully"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Cart is empty or not found"
     *      )
     * )
     * 
     * Estimate checkout costs before creating order
     */
    public function estimateCheckout(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart || $cart->items()->count() === 0) {
            return response()->json([
                'message' => 'Cart is empty or not found'
            ], 400);
        }

        $checkoutData = $request->validate([
            'shipping_country' => 'required|string|max:2',
            'billing_country' => 'nullable|string|max:2',
            'shipping_city' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:card,credit,bank_transfer'
        ]);

        try {
            $estimate = $this->checkoutCoordinator->estimateCheckout($cart, $checkoutData);

            return response()->json([
                'data' => $estimate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to estimate checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}