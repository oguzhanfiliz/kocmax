<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class PendingOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Processing,
            OrderStatus::Cancelled
        ]);
    }

    public function process(Order $order): void
    {
        // Pending state processing
        // - Check payment status
        // - Validate inventory
        // - Prepare for processing
        
        Log::debug('Processing pending order', ['order_id' => $order->id]);
        
        if ($this->shouldAutoTransitionToProcessing($order)) {
            // Automatically transition to processing if conditions are met
            app(\App\Services\Order\OrderStatusService::class)->updateStatus(
                $order, 
                OrderStatus::Processing, 
                null, 
                'Automatic transition: payment confirmed and inventory available'
            );
        }
    }

    public function getAvailableActions(): array
    {
        return [
            'process_payment' => 'Process Payment',
            'update_payment_status' => 'Update Payment Status',
            'cancel_order' => 'Cancel Order',
            'update_details' => 'Update Order Details',
            'add_note' => 'Add Note'
        ];
    }

    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Processing => 'Mark as Processing',
            OrderStatus::Cancelled => 'Cancel Order'
        ];
    }

    public function enter(Order $order): void
    {
        Log::info('Order entered pending state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Actions to perform when entering pending state
        $this->validateOrderData($order);
        $this->checkInventoryAvailability($order);
        $this->schedulePaymentReminder($order);
    }

    public function exit(Order $order): void
    {
        Log::info('Order exiting pending state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Actions to perform when exiting pending state
        $this->cancelPaymentReminder($order);
    }

    private function shouldAutoTransitionToProcessing(Order $order): bool
    {
        return $order->isPaid() && $this->hasInventoryAvailable($order);
    }

    private function hasInventoryAvailable(Order $order): bool
    {
        foreach ($order->items as $item) {
            if ($item->productVariant) {
                if ($item->productVariant->stock < $item->quantity) {
                    return false;
                }
            } elseif ($item->product) {
                if ($item->product->stock < $item->quantity) {
                    return false;
                }
            }
        }
        return true;
    }

    private function validateOrderData(Order $order): void
    {
        $issues = [];

        // Check if order has items
        if ($order->items->isEmpty()) {
            $issues[] = 'Order has no items';
        }

        // Check if required addresses are present
        if (empty($order->shipping_address)) {
            $issues[] = 'Missing shipping address';
        }

        if (empty($order->billing_address)) {
            $issues[] = 'Missing billing address';
        }

        // Check if total amount makes sense
        if ($order->total_amount <= 0) {
            $issues[] = 'Invalid total amount';
        }

        if (!empty($issues)) {
            Log::warning('Order validation issues in pending state', [
                'order_id' => $order->id,
                'issues' => $issues
            ]);
        }
    }

    private function checkInventoryAvailability(Order $order): void
    {
        $unavailableItems = [];

        foreach ($order->items as $item) {
            $availableStock = 0;
            
            if ($item->productVariant) {
                $availableStock = $item->productVariant->stock;
            } elseif ($item->product) {
                $availableStock = $item->product->stock;
            }

            if ($availableStock < $item->quantity) {
                $unavailableItems[] = [
                    'item_id' => $item->id,
                    'product_name' => $item->product_name,
                    'requested' => $item->quantity,
                    'available' => $availableStock
                ];
            }
        }

        if (!empty($unavailableItems)) {
            Log::warning('Inventory shortage detected for pending order', [
                'order_id' => $order->id,
                'unavailable_items' => $unavailableItems
            ]);
        }
    }

    private function schedulePaymentReminder(Order $order): void
    {
        // In a real implementation, this would schedule a job or notification
        // to remind the customer about pending payment
        Log::debug('Payment reminder scheduled for pending order', [
            'order_id' => $order->id
        ]);
    }

    private function cancelPaymentReminder(Order $order): void
    {
        // Cancel any scheduled payment reminders
        Log::debug('Payment reminder cancelled for order', [
            'order_id' => $order->id
        ]);
    }
}