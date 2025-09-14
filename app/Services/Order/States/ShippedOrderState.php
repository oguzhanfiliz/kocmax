<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class ShippedOrderState implements OrderStateInterface
{
    /**
     * Bu durumdan hangi durumlara geçilebileceğini kontrol eder.
     *
     * Not: Kargoya verilmiş (shipped) siparişler genellikle iptal edilemez.
     *
     * @param OrderStatus $newStatus Hedef durum
     * @return bool Geçiş mümkün mü
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Delivered
            // Not: Kargoya verilen siparişler genellikle iptal edilemez
        ]);
    }

    /**
     * Shipped (kargoya verildi) durumunda yapılacak işlemleri yürütür.
     *
     * - Takip durumunu izle
     * - Teslimat tahminlerini güncelle
     * - Teslimat bildirimlerini yönet
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function process(Order $order): void
    {
        // Shipped durumu işleme
        // - Takip durumunu izle
        // - Teslimat tahminlerini güncelle
        // - Teslimat bildirimlerini yönet
        
        Log::debug('Processing shipped order', ['order_id' => $order->id]);
        
        $this->updateTrackingStatus($order);
        $this->checkDeliveryStatus($order);
        $this->sendTrackingNotifications($order);
    }

    /**
     * Bu durumdayken yapılabilecek eylemleri döndürür.
     *
     * @return array Eylemler
     */
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

    /**
     * Bu durumdan yapılabilecek geçişleri döndürür.
     *
     * @return array Geçişler
     */
    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Delivered => 'Mark as Delivered'
        ];
    }

    /**
     * Shipped durumuna girildiğinde yapılacak işlemleri yürütür.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function enter(Order $order): void
    {
        Log::info('Order entered shipped state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'tracking_number' => $order->tracking_number
        ]);

        // Shipped durumuna girerken yapılacak işlemler
        $this->initializeTracking($order);
        $this->notifyCustomerShipped($order);
        $this->scheduleDeliveryUpdates($order);
    }

    /**
     * Shipped durumundan çıkarken yapılacak işlemleri yürütür.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function exit(Order $order): void
    {
        Log::info('Order exiting shipped state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Shipped durumundan çıkarken yapılacak işlemler
        $this->cancelDeliveryUpdates($order);
        $this->finalizeShippingData($order);
    }

    /**
     * Takip numarası ile takip işlemlerini başlatır.
     */
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

        // Takip izleme sürecini başlat
        $this->startTrackingMonitoring($order);
    }

    /**
     * Müşteriye kargo bilgilerini iletir (örnek).
     */
    private function notifyCustomerShipped(Order $order): void
    {
        // Müşteriye kargo onayı gönder
        Log::info('Sending shipping notification to customer', [
            'order_id' => $order->id,
            'customer_email' => $order->shipping_email ?? $order->billing_email
        ]);

        // Gerçek uygulamada, takip bilgilerini içeren e-posta/SMS gönderilir
    }

    /**
     * Teslimat güncellemelerini planlar (örnek).
     */
    private function scheduleDeliveryUpdates(Order $order): void
    {
        // Periyodik takip güncellemelerini planla
        Log::debug('Scheduling delivery updates for order', [
            'order_id' => $order->id
        ]);

        // Gerçek uygulamada, belirli aralıklarla takip durumunu kontrol eden işler planlanır
    }

    /**
     * Takip durumunu günceller ve gerekiyorsa otomatik durum geçişi yapar.
     */
    private function updateTrackingStatus(Order $order): void
    {
        if (empty($order->tracking_number)) {
            return;
        }

        Log::debug('Updating tracking status for order', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);

        // Takip durumu güncellemesi (örnek/mock)
        $trackingInfo = $this->fetchTrackingInfo($order);
        
        if ($trackingInfo && $trackingInfo['status'] === 'delivered') {
            // Takip bilgisine göre otomatik olarak delivered durumuna geç
            app(\App\Services\Order\OrderStatusService::class)->updateStatus(
                $order,
                OrderStatus::Delivered,
                null,
                'Automatic transition: package delivered according to tracking'
            );
        }
    }

    /**
     * Teslimat durumunu kontrol eder ve anomali varsa uyarı verir.
     */
    private function checkDeliveryStatus(Order $order): void
    {
        // Siparişin çok uzun süre teslim edilmeden kargoda kalıp kalmadığını kontrol et
        $shippedAt = $order->shipped_at;
        $maxDeliveryDays = $this->getMaxDeliveryDays($order);
        
        if ($shippedAt && now()->diffInDays($shippedAt) > $maxDeliveryDays) {
            Log::warning('Order shipped for extended period without delivery', [
                'order_id' => $order->id,
                'shipped_at' => $shippedAt,
                'days_since_shipped' => now()->diffInDays($shippedAt),
                'max_delivery_days' => $maxDeliveryDays
            ]);
            
            // Araştırma başlat veya müşteri ile iletişime geç
            $this->triggerDeliveryInvestigation($order);
        }
    }

    /**
     * Müşteriye periyodik takip bildirimleri gönderir (örnek).
     */
    private function sendTrackingNotifications(Order $order): void
    {
        // Müşteriye periyodik takip güncellemeleri gönder
        if ($order->tracking_number) {
            Log::debug('Sending tracking notification', [
                'order_id' => $order->id,
                'tracking_number' => $order->tracking_number
            ]);
        }
    }

    /**
     * Takip izleme sürecini başlatır (örnek).
     */
    private function startTrackingMonitoring(Order $order): void
    {
        // Takip durum değişikliklerini izlemeyi başlat
        Log::debug('Starting tracking monitoring', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);
    }

    /**
     * Planlanmış teslimat güncellemelerini iptal eder.
     */
    private function cancelDeliveryUpdates(Order $order): void
    {
        // Planlanmış teslimat güncelleme işlerini iptal et
        Log::debug('Cancelling delivery updates', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Shipped durumundan çıkarken gönderim verilerini finalize eder.
     */
    private function finalizeShippingData(Order $order): void
    {
        // Shipped durumundan çıkarken gönderim verilerini tamamla
        Log::debug('Finalizing shipping data', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Takip bilgilerini mock olarak getirir (örnek).
     *
     * @return array|null Takip bilgileri veya null
     */
    private function fetchTrackingInfo(Order $order): ?array
    {
        // Mock takip API çağrısı
        if (empty($order->tracking_number)) {
            return null;
        }

        // Mock takip durumu — gerçek uygulamada kargo firmasının takip API'si çağrılır
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

    /**
     * Kargo yöntemi/konuma göre maksimum beklenen teslimat gün sayısını döndürür.
     */
    private function getMaxDeliveryDays(Order $order): int
    {
        // Kargo yöntemi/konuma göre maksimum beklenen teslimat gün sayısını döndür
        $defaultDays = 7; // Varsayılan 1 hafta
        
        // Ülke, kargo yöntemi vb. baz alınarak ayarla
        if ($order->shipping_country !== 'TR') {
            $defaultDays = 14; // Uluslararası gönderi
        }
        
        return $defaultDays;
    }

    /**
     * Geciken teslimat için araştırma sürecini tetikler (örnek).
     */
    private function triggerDeliveryInvestigation(Order $order): void
    {
        Log::info('Triggering delivery investigation for delayed order', [
            'order_id' => $order->id,
            'tracking_number' => $order->tracking_number
        ]);

        // Gerçek uygulamada şunlar yapılır:
        // - Kargo firmasıyla iletişime geç
        // - Müşteri hizmetlerini bilgilendir
        // - Müşteriye proaktif bilgilendirme gönder
    }
}