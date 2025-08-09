<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    /**
     * Send order created notification
     */
    public function sendOrderCreated(Order $order): void
    {
        try {
            // Log the order creation
            Log::info('Order created notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                'total_amount' => $order->total_amount
            ]);

            // TODO: Implement email notification
            // Mail::to($order->user ?? $order->billing_email)
            //     ->send(new OrderCreatedMail($order));

            // TODO: Implement SMS notification if phone provided
            // if ($order->shipping_phone) {
            //     SMS::send($order->shipping_phone, "Siparişiniz #{$order->order_number} oluşturuldu.");
            // }

        } catch (\Exception $e) {
            Log::error('Failed to send order created notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send order status changed notification
     */
    public function sendOrderStatusChanged(Order $order, OrderStatus $oldStatus, OrderStatus $newStatus): void
    {
        try {
            $message = $this->getStatusChangeMessage($newStatus, $order);

            Log::info('Order status changed notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'message' => $message
            ]);

            // TODO: Implement email notification
            // Mail::to($order->user ?? $order->billing_email)
            //     ->send(new OrderStatusChangedMail($order, $message));

        } catch (\Exception $e) {
            Log::error('Failed to send order status changed notification', [
                'order_id' => $order->id,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send order shipped notification
     */
    public function sendOrderShipped(Order $order): void
    {
        try {
            Log::info('Order shipped notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'tracking_number' => $order->tracking_number
            ]);

            // TODO: Implement shipping notification
            // Mail::to($order->user ?? $order->billing_email)
            //     ->send(new OrderShippedMail($order));

        } catch (\Exception $e) {
            Log::error('Failed to send order shipped notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send order delivered notification
     */
    public function sendOrderDelivered(Order $order): void
    {
        try {
            Log::info('Order delivered notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // TODO: Implement delivery notification
            // Mail::to($order->user ?? $order->billing_email)
            //     ->send(new OrderDeliveredMail($order));

        } catch (\Exception $e) {
            Log::error('Failed to send order delivered notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send order cancelled notification
     */
    public function sendOrderCancelled(Order $order): void
    {
        try {
            Log::info('Order cancelled notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // TODO: Implement cancellation notification
            // Mail::to($order->user ?? $order->billing_email)
            //     ->send(new OrderCancelledMail($order));

        } catch (\Exception $e) {
            Log::error('Failed to send order cancelled notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get status change message
     */
    private function getStatusChangeMessage(OrderStatus $status, Order $order): string
    {
        return match($status) {
            OrderStatus::Processing => "Siparişiniz hazırlanıyor.",
            OrderStatus::Shipped => "Siparişiniz kargoya verildi." . 
                ($order->tracking_number ? " Takip numarası: {$order->tracking_number}" : ""),
            OrderStatus::Delivered => "Siparişiniz teslim edildi.",
            OrderStatus::Cancelled => "Siparişiniz iptal edildi.",
            default => "Sipariş durumu güncellendi."
        };
    }
}
