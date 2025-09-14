<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class DeliveredOrderState implements OrderStateInterface
{
    /**
     * Teslim edilmiş durumdan geçişe izin verilip verilmediğini döndürür.
     * Not: Teslim durumu genellikle nihai bir durumdur.
     *
     * @param OrderStatus $newStatus Hedef durum
     * @return bool Geçiş mümkün mü
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        // Teslim durumu genellikle nihai bir durumdur
        // Bazı sistemler iade/geri ödeme durumlarına izin verebilir, ancak şimdilik nihai tutacağız
        return false;
    }

    /**
     * Teslim edilmiş sipariş için yapılacak işlemleri yürütür.
     *
     * - Tamamlama bildirimlerini gönder
     * - Müşteri geri bildirimi iste
     * - Varsa son işlemleri gerçekleştir
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function process(Order $order): void
    {
        // Teslim durumu işleme adımları
        // - Tamamlama bildirimlerini gönder
        // - Müşteri geri bildirimi iste
        // - Varsa son işlemleri gerçekleştir
        
        Log::debug('Processing delivered order', ['order_id' => $order->id]);
        
        $this->sendDeliveryConfirmation($order);
        $this->requestCustomerFeedback($order);
        $this->processFinalTasks($order);
    }

    /**
     * Bu durumdayken yapılabilecek eylemleri döndürür.
     *
     * @return array Eylemler
     */
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

    /**
     * Bu durumdan yapılabilecek geçişleri döndürür (genellikle yoktur).
     *
     * @return array Geçişler
     */
    public function getAvailableTransitions(): array
    {
        // Teslim durumundan geçiş yok
        return [];
    }

    /**
     * Teslim durumuna girişte yapılacak işlemleri yürütür.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function enter(Order $order): void
    {
        Log::info('Order entered delivered state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'delivered_at' => $order->delivered_at
        ]);

        // Teslim durumuna girildiğinde yapılacak işlemler
        $this->recordDeliveryTime($order);
        $this->notifyStakeholders($order);
        $this->processCompletionTasks($order);
        $this->updateCustomerMetrics($order);
    }

    /**
     * Teslim durumundan çıkış denemesinde loglama yapar (normalde çağrılmaz).
     */
    public function exit(Order $order): void
    {
        // Teslim durumu genellikle nihai olduğundan bu metod normalde çağrılmamalıdır
        Log::warning('Attempting to exit delivered state - this should not normally happen', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);
    }

    /**
     * Teslim zamanını ve metriklerini kaydeder.
     */
    private function recordDeliveryTime(Order $order): void
    {
        if (empty($order->delivered_at)) {
            $order->update(['delivered_at' => now()]);
        }

        // Teslimat metriklerini hesapla
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

    /**
     * Teslimat durumunda ilgili paydaşları bilgilendirir.
     */
    private function notifyStakeholders(Order $order): void
    {
        // Müşteriyi bilgilendir
        $this->notifyCustomer($order);
        
        // Dahili ekipleri bilgilendir
        $this->notifyInternalTeams($order);
        
        // Analitikleri güncelle
        $this->updateAnalytics($order);
    }

    /**
     * Teslim sonrası tamamlama görevlerini yürütür.
     */
    private function processCompletionTasks(Order $order): void
    {
        Log::debug('Processing completion tasks for delivered order', [
            'order_id' => $order->id
        ]);

        // Uygunsa sadakat puanlarını işle
        if ($order->user) {
            $this->processLoyaltyPoints($order);
        }

        // Nihai fatura/fiş üret
        $this->generateFinalReceipt($order);
        
        // Satış metriklerini güncelle
        $this->updateSalesMetrics($order);
    }

    /**
     * Müşteri metriklerini günceller (gerçek uygulamada veri katmanları etkilenir).
     */
    private function updateCustomerMetrics(Order $order): void
    {
        if (!$order->user) {
            return;
        }

        Log::debug('Updating customer metrics for delivered order', [
            'order_id' => $order->id,
            'user_id' => $order->user_id
        ]);

        // Gerçek uygulamada; müşteri yaşam boyu değeri, sipariş sıklığı,
        // ortalama sipariş değeri vb. güncellenir
    }

    /**
     * Müşteriye teslim onayı gönderir (örnek log).
     */
    private function sendDeliveryConfirmation(Order $order): void
    {
        Log::info('Sending delivery confirmation', [
            'order_id' => $order->id,
            'customer_email' => $order->shipping_email ?? $order->billing_email
        ]);

        // Gerçek uygulamada teslimat onay e-posta/SMS gönderilir
    }

    /**
     * Müşteri geri bildirimi talebini planlar (örnek log).
     */
    private function requestCustomerFeedback(Order $order): void
    {
        Log::debug('Requesting customer feedback for delivered order', [
            'order_id' => $order->id
        ]);

        // Geri bildirim talebini planla (genelde teslimattan birkaç gün sonra)
        // Gerçek uygulamada bu işlem için bir iş planlanır
    }

    /**
     * Son işlem adımlarını yürütür (örnek log).
     */
    private function processFinalTasks(Order $order): void
    {
        // Son temizlik veya işlem adımları
        Log::debug('Processing final tasks for delivered order', [
            'order_id' => $order->id
        ]);
    }

    /**
     * Müşteriye teslim bildirimini iletir (örnek log).
     */
    private function notifyCustomer(Order $order): void
    {
        $customerEmail = $order->shipping_email ?? $order->billing_email;
        
        if ($customerEmail) {
            Log::info('Sending delivery notification to customer', [
                'order_id' => $order->id,
                'customer_email' => $customerEmail
            ]);
            
            // Gerçek uygulamada teslim onay e-postası gönderilir
        }
    }

    /**
     * İç ekipleri bilgilendirir (örnek log).
     */
    private function notifyInternalTeams(Order $order): void
    {
        Log::debug('Notifying internal teams of order delivery', [
            'order_id' => $order->id
        ]);

        // Müşteri hizmetleri, satış ve analitik ekiplerini bilgilendir
    }

    /**
     * Analitik veri kaynaklarını günceller (örnek log).
     */
    private function updateAnalytics(Order $order): void
    {
        Log::debug('Updating analytics for delivered order', [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'customer_type' => $order->customer_type
        ]);

        // Gelir analitiği, dönüşüm metrikleri vb. güncellenir
    }

    /**
     * Sadakat puanlarını hesaplar ve uygular (örnek log).
     */
    private function processLoyaltyPoints(Order $order): void
    {
        if (!$order->user) {
            return;
        }

        // Sipariş değerine göre sadakat puanlarını hesapla
        $pointsRate = $order->customer_type === 'B2B' ? 0.01 : 0.02; // B2C daha fazla puan alır
        $points = intval($order->total_amount * $pointsRate);

        if ($points > 0) {
            Log::info('Processing loyalty points for delivered order', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'points_awarded' => $points
            ]);

            // Gerçek uygulamada kullanıcının sadakat hesabına puan eklenir
        }
    }

    /**
     * Nihai makbuz/fatura üretir (örnek log).
     */
    private function generateFinalReceipt(Order $order): void
    {
        Log::debug('Generating final receipt for delivered order', [
            'order_id' => $order->id
        ]);

        // Nihai makbuz/fatura üret ve sakla
        // Eğer düzeltmeler olduysa bu, ilk faturadan farklı olabilir
    }

    /**
     * Satış metriklerini günceller (örnek log).
     */
    private function updateSalesMetrics(Order $order): void
    {
        Log::debug('Updating sales metrics for delivered order', [
            'order_id' => $order->id,
            'total_amount' => $order->total_amount,
            'customer_type' => $order->customer_type
        ]);

        // Günlük/aylık/yıllık satış metriklerini güncelle
        // Ürün performans metriklerini güncelle
        // Müşteri segment performansını güncelle
    }
}