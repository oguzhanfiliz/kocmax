<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Mail\OrderCreatedMail;
use App\Mail\OrderStatusChangedMail;
use App\Mail\OrderShippedMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderCancelledMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderNotificationService
{
    /**
     * Sipariş oluşturuldu bildirimini gönderir.
     */
    public function sendOrderCreated(Order $order): void
    {
        try {
            // Sipariş oluşturma kaydını logla
            Log::info('Order created notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                'total_amount' => $order->total_amount
            ]);

            // Müşteriye e-posta bildirimi gönder
            $recipient = $order->user ? $order->user->email : $order->billing_email;
            if ($recipient) {
                Mail::to($recipient)->send(new OrderCreatedMail($order));
                Log::info('Order created email sent', ['recipient' => $recipient]);
            }

            // Yöneticiye e-posta bildirimi gönder
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail && $adminEmail !== $recipient) {
                Mail::to($adminEmail)->send(new OrderCreatedMail($order, attachPdf: true));
                Log::info('Order created admin email sent', ['admin' => $adminEmail]);
            }

            // TODO: Telefon numarası mevcutsa SMS bildirimi uygulanabilir
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
     * Sipariş durum değişikliği bildirimini gönderir.
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

            // Müşteriye e-posta bildirimi gönder
            $recipient = $order->user ? $order->user->email : $order->billing_email;
            if ($recipient) {
                Mail::to($recipient)->send(new OrderStatusChangedMail($order, $message));
                Log::info('Order status changed email sent', ['recipient' => $recipient]);
            }

            // Ödeme tamamlandıysa (Processing), yöneticiyi de bilgilendir
            if ($newStatus === OrderStatus::Processing) {
                $adminEmail = $this->getAdminEmail();
                if ($adminEmail && $adminEmail !== $recipient) {
                    Mail::to($adminEmail)->send(new OrderStatusChangedMail($order, $message, attachPdf: true));
                    Log::info('Order status changed admin email sent', [
                        'admin' => $adminEmail,
                        'status' => $newStatus->value,
                    ]);
                }
            }

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
     * Sipariş kargoya verildi bildirimini gönderir.
     */
    public function sendOrderShipped(Order $order): void
    {
        try {
            Log::info('Order shipped notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'tracking_number' => $order->tracking_number
            ]);

            // Müşteriye e-posta bildirimi gönder
            $recipient = $order->user ? $order->user->email : $order->billing_email;
            if ($recipient) {
                Mail::to($recipient)->send(new OrderShippedMail($order));
                Log::info('Order shipped email sent', ['recipient' => $recipient]);
            }

            // Yöneticiye e-posta bildirimi gönder
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail && $adminEmail !== $recipient) {
                Mail::to($adminEmail)->send(new OrderShippedMail($order, attachPdf: true));
                Log::info('Order shipped admin email sent', ['admin' => $adminEmail]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order shipped notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sipariş teslim edildi bildirimini gönderir.
     */
    public function sendOrderDelivered(Order $order): void
    {
        try {
            Log::info('Order delivered notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Müşteriye e-posta bildirimi gönder
            $recipient = $order->user ? $order->user->email : $order->billing_email;
            if ($recipient) {
                Mail::to($recipient)->send(new OrderDeliveredMail($order));
                Log::info('Order delivered email sent', ['recipient' => $recipient]);
            }

            // Yöneticiye e-posta bildirimi gönder
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail && $adminEmail !== $recipient) {
                Mail::to($adminEmail)->send(new OrderDeliveredMail($order, attachPdf: true));
                Log::info('Order delivered admin email sent', ['admin' => $adminEmail]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order delivered notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sipariş iptal edildi bildirimini gönderir.
     */
    public function sendOrderCancelled(Order $order): void
    {
        try {
            Log::info('Order cancelled notification', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Müşteriye e-posta bildirimi gönder
            $recipient = $order->user ? $order->user->email : $order->billing_email;
            if ($recipient) {
                Mail::to($recipient)->send(new OrderCancelledMail($order));
                Log::info('Order cancelled email sent', ['recipient' => $recipient]);
            }

            // Yöneticiye e-posta bildirimi gönder
            $adminEmail = $this->getAdminEmail();
            if ($adminEmail && $adminEmail !== $recipient) {
                Mail::to($adminEmail)->send(new OrderCancelledMail($order, attachPdf: true));
                Log::info('Order cancelled admin email sent', ['admin' => $adminEmail]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send order cancelled notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Durum değişikliği mesajını döndürür.
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

    private function getAdminEmail(): ?string
    {
        // Öncelik sırası: env(ORDER_ADMIN_EMAIL) -> config('mail.admin_email') -> yedek değer
        return env('ORDER_ADMIN_EMAIL')
            ?: config('mail.admin_email', 'info@kocmax.tr');
    }
}
