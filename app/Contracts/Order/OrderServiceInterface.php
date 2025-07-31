<?php

declare(strict_types=1);

namespace App\Contracts\Order;

use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatus;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderSummary;
use App\ValueObjects\Order\PaymentResult;

interface OrderServiceInterface
{
    /**
     * Create order from cart checkout context
     */
    public function createFromCheckout(CheckoutContext $context, array $orderData): Order;

    /**
     * Update order status with validation
     */
    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void;

    /**
     * Cancel order with inventory restoration
     */
    public function cancelOrder(Order $order, ?User $cancelledBy = null, ?string $reason = null): void;

    /**
     * Calculate order summary
     */
    public function calculateSummary(Order $order): OrderSummary;

    /**
     * Process payment for order
     */
    public function processPayment(Order $order, array $paymentData): PaymentResult;

    /**
     * Process refund for order
     */
    public function processRefund(Order $order, float $refundAmount, ?string $reason = null): PaymentResult;

    /**
     * Mark order as shipped
     */
    public function markAsShipped(Order $order, ?string $trackingNumber = null, ?string $carrier = null, ?User $shippedBy = null): void;

    /**
     * Mark order as delivered
     */
    public function markAsDelivered(Order $order, ?User $deliveredBy = null): void;

    /**
     * Get order status history
     */
    public function getStatusHistory(Order $order): array;

    /**
     * Validate order data
     */
    public function validateOrder(Order $order): \App\ValueObjects\Order\OrderValidationResult;
}