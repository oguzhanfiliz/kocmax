<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class PendingOrderState implements OrderStateInterface
{
    /**
     * Bu durumdan hangi durumlara geçilebileceğini belirtir.
     *
     * @param OrderStatus $newStatus Hedef durum
     * @return bool Geçiş mümkün mü
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Processing,
            OrderStatus::Cancelled
        ]);
    }

    /**
     * Pending durumunda yapılacak işlemleri yürütür.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function process(Order $order): void
    {
        // Pending durumunda yapılacak işlemler
        // - Ödeme durumunu kontrol et
        // - Stokları doğrula
        // - Processing için hazırla
        
        Log::debug('Processing pending order', ['order_id' => $order->id]);
        
        if ($this->shouldAutoTransitionToProcessing($order)) {
            // Koşullar sağlanırsa otomatik olarak processing durumuna geç
            app(\App\Services\Order\OrderStatusService::class)->updateStatus(
                $order, 
                OrderStatus::Processing, 
                null, 
                'Automatic transition: payment confirmed and inventory available'
            );
        }
    }

    /**
     * Kullanılabilir eylemleri döndürür.
     *
     * @return array Eylemler
     */
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

    /**
     * Yapılabilir durum geçişlerini döndürür.
     *
     * @return array Geçişler
     */
    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Processing => 'Mark as Processing',
            OrderStatus::Cancelled => 'Cancel Order'
        ];
    }

    /**
     * Pending durumuna girildiğinde yapılacak işlemler.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function enter(Order $order): void
    {
        Log::info('Order entered pending state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Pending durumuna girerken yapılacak işlemler
        $this->validateOrderData($order);
        $this->checkInventoryAvailability($order);
        $this->schedulePaymentReminder($order);
    }

    /**
     * Pending durumundan çıkarken yapılacak işlemler.
     */
    public function exit(Order $order): void
    {
        Log::info('Order exiting pending state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Pending durumundan çıkarken yapılacak işlemler
        $this->cancelPaymentReminder($order);
    }

    /**
     * Koşullar sağlanırsa otomatik processing durumuna geçilmesi gerekip gerekmediğini belirler.
     */
    private function shouldAutoTransitionToProcessing(Order $order): bool
    {
        return $order->isPaid() && $this->hasInventoryAvailable($order);
    }

    /**
     * Sipariş için gereken stokların mevcut olup olmadığını kontrol eder.
     */
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

    /**
     * Pending durumunda sipariş verilerini doğrular.
     */
    private function validateOrderData(Order $order): void
    {
        $issues = [];

        // Siparişte öğe var mı kontrol et
        if ($order->items->isEmpty()) {
            $issues[] = 'Order has no items';
        }

        // Gerekli adresler mevcut mu kontrol et
        if (empty($order->shipping_address)) {
            $issues[] = 'Missing shipping address';
        }

        if (empty($order->billing_address)) {
            $issues[] = 'Missing billing address';
        }

        // Toplam tutar mantıklı mı kontrol et
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

    /**
     * Stok uygunluğunu kontrol eder ve sorunlu öğeleri loglar.
     */
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

    /**
     * Ödeme hatırlatıcısı planlar (örnek uygulama).
     */
    private function schedulePaymentReminder(Order $order): void
    {
        // Gerçek uygulamada, bekleyen ödeme için müşteriyi hatırlatacak bir job/notification planlanır
        Log::debug('Payment reminder scheduled for pending order', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Planlanmış ödeme hatırlatıcısını iptal eder.
     */
    private function cancelPaymentReminder(Order $order): void
    {
        // Planlanmış ödeme hatırlatmalarını iptal et
        Log::debug('Payment reminder cancelled for order', [
            'order_id' => $order->id
        ]);
    }
}