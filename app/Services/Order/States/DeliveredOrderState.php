<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class DeliveredOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        // Delivered is typically a final state
        // Some systems might allow return/refund states, but for now we'll keep it final
        return false;
    }

    public function process(Order $order): void
    {
        // Delivered state processing
        // - Send completion notifications
        // - Request customer feedback
        // - Process any final tasks
        
        Log::debug('Processing delivered order', ['order_id' => $order->id]);
        
        $this->sendDeliveryConfirmation($order);
        $this->requestCustomerFeedback($order);
        $this->processFinalTasks($order);
    }

    public function getAvailableActions(): array
    {
        return [
            'send_feedback_request' => 'Request Customer Feedback',
            'generate_receipt' => 'Generate Final Receipt',
            'process_loyalty_points' => 'Process Loyalty Points',
            'add_note' => 'Add Note',
            'export_data' => 'Export Order Data',
            'archive_order' => 'Archive Order'
        ];
    }

    public function getAvailableTransitions(): array
    {
        // No transitions available from delivered state
        return [];
    }

    public function enter(Order $order): void
    {
        Log::info('Order entered delivered state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'delivered_at' => $order->delivered_at
        ]);

        // Actions to perform when entering delivered state
        $this->recordDeliveryTime($order);
        $this->notifyStakeholders($order);
        $this->processCompletionTasks($order);
        $this->updateCustomerMetrics($order);
    }

    public function exit(Order $order): void
    {
        // Delivered state is typically final, so this shouldn't be called
        Log::warning('Attempting to exit delivered state - this should not normally happen', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }

    private function recordDeliveryTime(Order $order): void
    {
        if (empty($order->delivered_at)) {
            $order->update(['delivered_at' => now()]);
        }

        // Calculate delivery metrics
        $totalTime = null;
        $processingTime = null;
        $shippingTime = null;

        if ($order->created_at) {
            $totalTime = $order->delivered_at->diffInHours($order->created_at);
        }

        if ($order->shipped_at) {
            $shippingTime = $order->delivered_at->diffInHours($order->shipped_at);
            
            if ($order->created_at) {
                $processingTime = $order->shipped_at->diffInHours($order->created_at);
            }
        }

        Log::info('Order delivery metrics recorded', [
            'order_id' => $order->id,
            'total_delivery_time_hours' => $totalTime,
            'processing_time_hours' => $processingTime,
            'shipping_time_hours' => $shippingTime
        ]);
    }

    private function notifyStakeholders(Order $order): void
    {
        // Notify customer
        $this->notifyCustomer($order);
        
        // Notify internal teams
        $this->notifyInternalTeams($order);
        
        // Update analytics
        $this->updateAnalytics($order);
    }

    private function processCompletionTasks(Order $order): void
    {
        Log::debug('Processing completion tasks for delivered order', [
            'order_id' => $order->id
        ]);

        // Process loyalty points if applicable
        if ($order->user) {
            $this->processLoyaltyPoints($order);
        }

        // Generate final invoice/receipt
        $this->generateFinalReceipt($order);
        
        // Update sales metrics
        $this->updateSalesMetrics($order);
    }

    private function updateCustomerMetrics(Order $order): void
    {
        if (!$order->user) {
            return;
        }

        Log::debug('Updating customer metrics for delivered order', [
            'order_id' => $order->id,
            'user_id' => $order->user_id
        ]);

        // In a real implementation, this would update customer lifetime value,
        // order frequency, average order value, etc.
    }

    private function sendDeliveryConfirmation(Order $order): void
    {
        Log::info('Sending delivery confirmation', [
            'order_id' => $order->id,
            'customer_email' => $order->shipping_email ?? $order->billing_email
        ]);

        // In a real implementation, this would send delivery confirmation email/SMS
    }

    private function requestCustomerFeedback(Order $order): void
    {
        Log::debug('Requesting customer feedback for delivered order', [
            'order_id' => $order->id
        ]);

        // Schedule feedback request (usually sent a few days after delivery)
        // In a real implementation, this would schedule a job to send feedback request
    }

    private function processFinalTasks(Order $order): void
    {
        // Any final cleanup or processing tasks
        Log::debug('Processing final tasks for delivered order', [
            'order_id' => $order->id
        ]);
    }

    private function notifyCustomer(Order $order): void
    {
        $customerEmail = $order->shipping_email ?? $order->billing_email;
        
        if ($customerEmail) {
            Log::info('Sending delivery notification to customer', [
                'order_id' => $order->id,
                'customer_email' => $customerEmail
            ]);
            
            // In a real implementation, send delivery confirmation email
        }
    }

    private function notifyInternalTeams(Order $order): void
    {
        Log::debug('Notifying internal teams of order delivery', [
            'order_id' => $order->id
        ]);

        // Notify customer service, sales, analytics teams
    }

    private function updateAnalytics(Order $order): void
    {
        Log::debug('Updating analytics for delivered order', [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'customer_type' => $order->customer_type
        ]);

        // Update revenue analytics, conversion metrics, etc.
    }

    private function processLoyaltyPoints(Order $order): void
    {
        if (!$order->user) {
            return;
        }

        // Calculate loyalty points based on order value
        $pointsRate = $order->customer_type === 'B2B' ? 0.01 : 0.02; // B2C gets more points
        $points = intval($order->total_amount * $pointsRate);

        if ($points > 0) {
            Log::info('Processing loyalty points for delivered order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'points_awarded' => $points
            ]);

            // In a real implementation, this would add points to user's loyalty account
        }
    }

    private function generateFinalReceipt(Order $order): void
    {
        Log::debug('Generating final receipt for delivered order', [
            'order_id' => $order->id
        ]);

        // Generate and store final receipt/invoice
        // This might be different from the initial invoice if there were any adjustments
    }

    private function updateSalesMetrics(Order $order): void
    {
        Log::debug('Updating sales metrics for delivered order', [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'customer_type' => $order->customer_type
        ]);

        // Update daily/monthly/yearly sales metrics
        // Update product performance metrics
        // Update customer segment performance
    }
}