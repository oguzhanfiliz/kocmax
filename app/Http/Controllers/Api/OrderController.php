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

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CheckoutCoordinator $checkoutCoordinator
    ) {}

    /**
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