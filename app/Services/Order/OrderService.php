<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Contracts\Order\OrderServiceInterface;
use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderSummary;
use App\ValueObjects\Order\PaymentResult;
use App\ValueObjects\Order\OrderValidationResult;
use App\Services\Order\OrderCreationService;
use App\Services\Order\OrderStatusService;
use App\Services\Order\OrderPaymentService;
use App\Services\Order\OrderValidationService;
use App\Services\Order\OrderNotificationService;
use App\Exceptions\Order\OrderCreationException;
use App\Exceptions\Order\InvalidStatusTransitionException;
use App\Exceptions\Order\OrderCancellationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderCreationService $creationService,
        private OrderStatusService $statusService,
        private OrderPaymentService $paymentService,
        private OrderValidationService $validationService,
        private OrderNotificationService $notificationService
    ) {}

    public function createFromCheckout(CheckoutContext $context, array $orderData): Order
    {
        try {
            // Validate order creation
            $validation = $this->validationService->validateOrderCreation($context, $orderData);
            if (!$validation->isValid()) {
                throw new OrderCreationException($validation->getErrors());
            }

            return DB::transaction(function() use ($context, $orderData) {
                // Create order entity
                $order = $this->creationService->createOrder($context, $orderData);
                
                // Create order items
                $this->creationService->createOrderItems($order, $context->getItems());
                
                // Set initial status
                $this->statusService->setInitialStatus($order);
                
                // Process payment if immediate payment required
                if ($this->requiresImmediatePayment($orderData)) {
                    $paymentResult = $this->paymentService->processPayment($order, $orderData['payment_data'] ?? []);
                    $this->handlePaymentResult($order, $paymentResult);
                }
                
                // Send notifications
                $this->notificationService->sendOrderCreated($order);
                
                Log::info('Order created successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount
                ]);
                
                return $order;
            });

        } catch (\Exception $e) {
            Log::error('Failed to create order from checkout', [
                'error' => $e->getMessage(),
                'context' => $context->toArray()
            ]);
            throw $e;
        }
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void
    {
        try {
            $currentState = $this->statusService->getOrderState($order);
            
            if (!$currentState->canTransitionTo($newStatus)) {
                throw new InvalidStatusTransitionException(
                    "Cannot transition from {$order->status} to {$newStatus->value}"
                );
            }

            $this->statusService->updateStatus($order, $newStatus, $updatedBy, $reason);
            
            // Handle status-specific actions
            $this->handleStatusChange($order, $newStatus);
            
            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $newStatus->value,
                'updated_by' => $updatedBy?->id,
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update order status', [
                'order_id' => $order->id,
                'new_status' => $newStatus->value,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function cancelOrder(Order $order, ?User $cancelledBy = null, ?string $reason = null): void
    {
        if (!$order->canBeCancelled()) {
            throw new OrderCancellationException("Order cannot be cancelled in current status: {$order->status}");
        }

        try {
            DB::transaction(function() use ($order, $cancelledBy, $reason) {
                // Update status to cancelled
                $this->updateStatus($order, OrderStatus::Cancelled, $cancelledBy, $reason);
                
                // Restore inventory
                $this->restoreInventory($order);
                
                // Process refund if payment was made
                if ($order->isPaid()) {
                    $this->processRefund($order, $order->total_amount, 'Order cancellation');
                }
                
                // Send cancellation notification
                $this->notificationService->sendOrderCancelled($order);
            });

            Log::info('Order cancelled successfully', [
                'order_id' => $order->id,
                'cancelled_by' => $cancelledBy?->id,
                'reason' => $reason
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function calculateSummary(Order $order): OrderSummary
    {
        $itemDetails = [];
        $itemCount = 0;

        foreach ($order->items as $item) {
            $itemCount += $item->quantity;
            $itemDetails[] = [
                'item_id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'discount_amount' => $item->discount_amount ?? 0,
                'tax_amount' => $item->tax_amount ?? 0,
                'total' => $item->total,
            ];
        }

        return new OrderSummary(
            subtotal: $order->subtotal,
            taxAmount: $order->tax_amount ?? 0,
            shippingAmount: $order->shipping_amount ?? 0,
            discountAmount: $order->discount_amount ?? 0,
            totalAmount: $order->total_amount,
            itemCount: $itemCount,
            itemDetails: $itemDetails,
            currency: $order->currency_code ?? 'TRY'
        );
    }

    public function processPayment(Order $order, array $paymentData): PaymentResult
    {
        return $this->paymentService->processPayment($order, $paymentData);
    }

    public function processRefund(Order $order, float $refundAmount, ?string $reason = null): PaymentResult
    {
        return $this->paymentService->processRefund($order, $refundAmount, $reason);
    }

    public function markAsShipped(Order $order, ?string $trackingNumber = null, ?string $carrier = null, ?User $shippedBy = null): void
    {
        $this->updateStatus($order, OrderStatus::Shipped, $shippedBy, 'Order shipped');
        
        $order->update([
            'tracking_number' => $trackingNumber,
            'shipping_carrier' => $carrier,
            'shipped_at' => now()
        ]);

        $this->notificationService->sendOrderShipped($order);
    }

    public function markAsDelivered(Order $order, ?User $deliveredBy = null): void
    {
        $this->updateStatus($order, OrderStatus::Delivered, $deliveredBy, 'Order delivered');
        
        $order->update([
            'delivered_at' => now()
        ]);

        $this->notificationService->sendOrderDelivered($order);
    }

    public function getStatusHistory(Order $order): array
    {
        return $order->statusHistory()
            ->with('changedBy')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function validateOrder(Order $order): OrderValidationResult
    {
        return $this->validationService->validateOrder($order);
    }

    private function requiresImmediatePayment(array $orderData): bool
    {
        $paymentMethod = $orderData['payment_method'] ?? 'card';
        
        // B2B credit payments don't require immediate processing
        return $paymentMethod !== 'credit';
    }

    private function handlePaymentResult(Order $order, PaymentResult $paymentResult): void
    {
        if ($paymentResult->isSuccess()) {
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $paymentResult->getTransactionId()
            ]);
            
            // Automatically move to processing if payment successful
            if ($order->status === 'pending') {
                $this->updateStatus($order, OrderStatus::Processing, null, 'Payment confirmed');
            }
        } else {
            $order->update([
                'payment_status' => 'failed',
                'notes' => ($order->notes ? $order->notes . "\n" : '') . 'Payment failed: ' . $paymentResult->getErrorMessage()
            ]);
        }
    }

    private function handleStatusChange(Order $order, OrderStatus $newStatus): void
    {
        match($newStatus) {
            OrderStatus::Processing => $this->handleProcessingStatus($order),
            OrderStatus::Shipped => $this->handleShippedStatus($order),
            OrderStatus::Delivered => $this->handleDeliveredStatus($order),
            OrderStatus::Cancelled => $this->handleCancelledStatus($order),
            default => null
        };
    }

    private function handleProcessingStatus(Order $order): void
    {
        // Reserve inventory
        foreach ($order->items as $item) {
            if ($item->productVariant) {
                $item->productVariant->decrement('stock', $item->quantity);
            } elseif ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }
    }

    private function handleShippedStatus(Order $order): void
    {
        // Notify shipping provider
        // Generate tracking information
        $this->notificationService->sendOrderShipped($order);
    }

    private function handleDeliveredStatus(Order $order): void
    {
        // Complete order
        // Send completion notifications
        $this->notificationService->sendOrderDelivered($order);
    }

    private function handleCancelledStatus(Order $order): void
    {
        // Additional cleanup if needed
        $this->notificationService->sendOrderCancelled($order);
    }

    private function restoreInventory(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->productVariant) {
                $item->productVariant->increment('stock', $item->quantity);
            } elseif ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}