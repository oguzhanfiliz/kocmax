<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class ShippedOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Delivered
            // Note: Shipped orders typically cannot be cancelled
        ]);
    }

    public function process(Order $order): void
    {
        // Shipped state processing
        // - Monitor tracking status
        // - Update delivery estimates
        // - Handle delivery notifications
        
        Log::debug('Processing shipped order', ['order_id' => $order->id]);
        
        $this->updateTrackingStatus($order);
        $this->checkDeliveryStatus($order);
        $this->sendTrackingNotifications($order);
    }

    public function getAvailableActions(): array
    {
        return [
            'update_tracking' => 'Update Tracking Information',
            'mark_delivered' => 'Mark as Delivered',
            'contact_carrier' => 'Contact Shipping Carrier',
            'update_delivery_estimate' => 'Update Delivery Estimate',
            'add_note' => 'Add Note',
            'send_tracking_info' => 'Send Tracking Info to Customer'
        ];
    }

    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Delivered => 'Mark as Delivered'
        ];
    }

    public function enter(Order $order): void
    {
        Log::info('Order entered shipped state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tracking_number' => $order->tracking_number
        ]);

        // Actions to perform when entering shipped state
        $this->initializeTracking($order);
        $this->notifyCustomerShipped($order);
        $this->scheduleDeliveryUpdates($order);
    }

    public function exit(Order $order): void
    {
        Log::info('Order exiting shipped state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Actions to perform when exiting shipped state
        $this->cancelDeliveryUpdates($order);
        $this->finalizeShippingData($order);
    }

    private function initializeTracking(Order $order): void
    {
        if (empty($order->tracking_number)) {
            Log::warning('Order shipped without tracking number', [
                'order_id' => $order->id
            ]);
            return;
        }

        Log::info('Tracking initialized for shipped order', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number,
            'carrier' => $order->shipping_carrier
        ]);

        // Initialize tracking monitoring
        $this->startTrackingMonitoring($order);
    }

    private function notifyCustomerShipped(Order $order): void
    {
        // Send shipping confirmation to customer
        Log::info('Sending shipping notification to customer', [
            'order_id' => $order->id,
            'customer_email' => $order->shipping_email ?? $order->billing_email
        ]);

        // In a real implementation, this would send an email/SMS
        // notification with tracking information
    }

    private function scheduleDeliveryUpdates(Order $order): void
    {
        // Schedule periodic tracking updates
        Log::debug('Scheduling delivery updates for order', [
            'order_id' => $order->id
        ]);

        // In a real implementation, this would schedule jobs to
        // periodically check tracking status
    }

    private function updateTrackingStatus(Order $order): void
    {
        if (empty($order->tracking_number)) {
            return;
        }

        Log::debug('Updating tracking status for order', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);

        // Mock tracking status update
        $trackingInfo = $this->fetchTrackingInfo($order);
        
        if ($trackingInfo && $trackingInfo['status'] === 'delivered') {
            // Automatically transition to delivered
            app(\App\Services\Order\OrderStatusService::class)->updateStatus(
                $order,
                OrderStatus::Delivered,
                null,
                'Automatic transition: package delivered according to tracking'
            );
        }
    }

    private function checkDeliveryStatus(Order $order): void
    {
        // Check if order has been shipped for too long without delivery
        $shippedAt = $order->shipped_at;
        $maxDeliveryDays = $this->getMaxDeliveryDays($order);
        
        if ($shippedAt && now()->diffInDays($shippedAt) > $maxDeliveryDays) {
            Log::warning('Order shipped for extended period without delivery', [
                'order_id' => $order->id,
                'shipped_at' => $shippedAt,
                'days_since_shipped' => now()->diffInDays($shippedAt),
                'max_delivery_days' => $maxDeliveryDays
            ]);
            
            // Trigger investigation or customer contact
            $this->triggerDeliveryInvestigation($order);
        }
    }

    private function sendTrackingNotifications(Order $order): void
    {
        // Send periodic tracking updates to customer
        if ($order->tracking_number) {
            Log::debug('Sending tracking notification', [
                'order_id' => $order->id,
                'tracking_number' => $order->tracking_number
            ]);
        }
    }

    private function startTrackingMonitoring(Order $order): void
    {
        // Start monitoring tracking status changes
        Log::debug('Starting tracking monitoring', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);
    }

    private function cancelDeliveryUpdates(Order $order): void
    {
        // Cancel scheduled delivery update jobs
        Log::debug('Cancelling delivery updates', [
            'order_id' => $order->id
        ]);
    }

    private function finalizeShippingData(Order $order): void
    {
        // Finalize shipping data when exiting shipped state
        Log::debug('Finalizing shipping data', [
            'order_id' => $order->id
        ]);
    }

    private function fetchTrackingInfo(Order $order): ?array
    {
        // Mock tracking API call
        if (empty($order->tracking_number)) {
            return null;
        }

        // Mock tracking status - in real implementation, this would
        // call the carrier's tracking API
        $mockStatuses = ['in_transit', 'out_for_delivery', 'delivered'];
        $randomStatus = $mockStatuses[array_rand($mockStatuses)];

        return [
            'tracking_number' => $order->tracking_number,
            'status' => $randomStatus,
            'location' => 'Distribution Center',
            'estimated_delivery' => now()->addDays(2)->toDateString(),
            'last_update' => now()->toISOString()
        ];
    }

    private function getMaxDeliveryDays(Order $order): int
    {
        // Return maximum expected delivery days based on shipping method/location
        $defaultDays = 7; // Default 1 week
        
        // Adjust based on country, shipping method, etc.
        if ($order->shipping_country !== 'TR') {
            $defaultDays = 14; // International shipping
        }
        
        return $defaultDays;
    }

    private function triggerDeliveryInvestigation(Order $order): void
    {
        Log::info('Triggering delivery investigation for delayed order', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);

        // In a real implementation, this would:
        // - Contact the shipping carrier
        // - Notify customer service
        // - Send proactive communication to customer
    }
}