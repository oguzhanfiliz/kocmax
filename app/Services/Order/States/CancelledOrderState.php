<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class CancelledOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        // Cancelled is typically a final state
        // In some systems, cancelled orders might be able to be reinstated,
        // but for now we'll keep it final
        return false;
    }

    public function process(Order $order): void
    {
        // Cancelled state processing
        // - Ensure refunds are processed
        // - Ensure inventory is restored
        // - Send cancellation notifications
        
        Log::debug('Processing cancelled order', ['order_id' => $order->id]);
        
        $this->verifyCancellationTasks($order);
        $this->sendCancellationNotifications($order);
        $this->updateCancellationMetrics($order);
    }

    public function getAvailableActions(): array
    {
        return [
            'send_cancellation_notice' => 'Send Cancellation Notice',
            'process_refund' => 'Process Refund',
            'restore_inventory' => 'Restore Inventory',
            'add_note' => 'Add Cancellation Note',
            'export_data' => 'Export Order Data',
            'archive_order' => 'Archive Order'
        ];
    }

    public function getAvailableTransitions(): array
    {
        // No transitions available from cancelled state
        return [];
    }

    public function enter(Order $order): void
    {
        Log::info('Order entered cancelled state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'cancelled_at' => now()
        ]);

        // Actions to perform when entering cancelled state
        $this->processInventoryRestoration($order);
        $this->processRefundIfNeeded($order);
        $this->notifyStakeholders($order);
        $this->recordCancellationMetrics($order);
    }

    public function exit(Order $order): void
    {
        // Cancelled state is typically final, so this shouldn't be called
        Log::warning('Attempting to exit cancelled state - this should not normally happen', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }

    private function processInventoryRestoration(Order $order): void
    {
        Log::info('Processing inventory restoration for cancelled order', [
            'order_id' => $order->id
        ]);

        foreach ($order->items as $item) {
            try {
                // Only restore inventory if it was previously reserved (i.e., order was in processing state)
                if ($order->getOriginal('status') === 'processing') {
                    if ($item->productVariant) {
                        $item->productVariant->increment('stock', $item->quantity);
                        Log::debug('Inventory restored for variant', [
                            'variant_id' => $item->productVariant->id,
                            'quantity_restored' => $item->quantity
                        ]);
                    } elseif ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                        Log::debug('Inventory restored for product', [
                            'product_id' => $item->product->id,
                            'quantity_restored' => $item->quantity
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error restoring inventory for cancelled order', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function processRefundIfNeeded(Order $order): void
    {
        if (!$order->isPaid()) {
            Log::debug('No refund needed for unpaid cancelled order', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status
            ]);
            return;
        }

        Log::info('Processing refund for cancelled order', [
            'order_id' => $order->id,
            'refund_amount' => $order->total_amount
        ]);

        try {
            // In a real implementation, this would call the payment service to process refund
            $paymentService = app(\App\Services\Order\OrderPaymentService::class);
            $refundResult = $paymentService->processRefund($order, $order->total_amount, 'Order cancellation');
            
            if ($refundResult->isSuccess()) {
                Log::info('Refund processed successfully for cancelled order', [
                    'order_id' => $order->id,
                    'refund_transaction_id' => $refundResult->getTransactionId()
                ]);
            } else {
                Log::error('Refund processing failed for cancelled order', [
                    'order_id' => $order->id,
                    'error' => $refundResult->getErrorMessage()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception during refund processing for cancelled order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function notifyStakeholders(Order $order): void
    {
        // Notify customer
        $this->notifyCustomer($order);
        
        // Notify internal teams
        $this->notifyInternalTeams($order);
    }

    private function recordCancellationMetrics(Order $order): void
    {
        $cancellationData = [
            'order_id' => $order->id,
            'cancelled_at' => now(),
            'original_status' => $order->getOriginal('status'),
            'order_value' => $order->total_amount,
            'customer_type' => $order->customer_type,
            'days_since_creation' => now()->diffInDays($order->created_at),
            'had_payment' => $order->isPaid()
        ];

        Log::info('Recording cancellation metrics', $cancellationData);
        
        // In a real implementation, this would store cancellation analytics data
    }

    private function verifyCancellationTasks(Order $order): void
    {
        $tasks = [
            'inventory_restored' => $this->verifyInventoryRestored($order),
            'refund_processed' => $this->verifyRefundProcessed($order),
            'notifications_sent' => $this->verifyNotificationsSent($order)
        ];

        Log::debug('Cancellation tasks verification', [
            'order_id' => $order->id,
            'tasks' => $tasks
        ]);

        $pendingTasks = array_filter($tasks, fn($completed) => !$completed);
        if (!empty($pendingTasks)) {
            Log::warning('Some cancellation tasks are pending', [
                'order_id' => $order->id,
                'pending_tasks' => array_keys($pendingTasks)
            ]);
        }
    }

    private function sendCancellationNotifications(Order $order): void
    {
        Log::debug('Sending cancellation notifications', ['order_id' => $order->id]);
        
        // Send to customer
        $this->notifyCustomer($order);
        
        // Send to internal teams
        $this->notifyInternalTeams($order);
    }

    private function updateCancellationMetrics(Order $order): void
    {
        Log::debug('Updating cancellation metrics', [
            'order_id' => $order->id,
            'order_value' => $order->total_amount
        ]);

        // Update cancellation rate metrics
        // Track cancellation reasons
        // Update customer cancellation history
    }

    private function notifyCustomer(Order $order): void
    {
        $customerEmail = $order->shipping_email ?? $order->billing_email;
        
        if ($customerEmail) {
            Log::info('Sending cancellation notification to customer', [
                'order_id' => $order->id,
                'customer_email' => $customerEmail
            ]);
            
            // In a real implementation, send cancellation confirmation email
        }
    }

    private function notifyInternalTeams(Order $order): void
    {
        Log::debug('Notifying internal teams of order cancellation', [
            'order_id' => $order->id,
            'order_value' => $order->total_amount
        ]);

        // Notify customer service, sales, warehouse teams
        // Alert if high-value order cancellation
        if ($order->total_amount > 1000) {
            Log::alert('High-value order cancelled', [
                'order_id' => $order->id,
                'order_value' => $order->total_amount,
                'customer_type' => $order->customer_type
            ]);
        }
    }

    private function verifyInventoryRestored(Order $order): bool
    {
        // In a real implementation, this would verify that inventory was properly restored
        return true;
    }

    private function verifyRefundProcessed(Order $order): bool
    {
        // In a real implementation, this would verify that refund was processed if needed
        return !$order->isPaid() || !empty($order->refund_transaction_id);
    }

    private function verifyNotificationsSent(Order $order): bool
    {
        // In a real implementation, this would verify that notifications were sent
        return true;
    }
}