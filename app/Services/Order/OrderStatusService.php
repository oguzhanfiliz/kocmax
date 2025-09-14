<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatusHistory;
use App\Contracts\Order\OrderStateInterface;
use App\Services\Order\States\PendingOrderState;
use App\Services\Order\States\ProcessingOrderState;
use App\Services\Order\States\ShippedOrderState;
use App\Services\Order\States\DeliveredOrderState;
use App\Services\Order\States\CancelledOrderState;
use App\Exceptions\Order\InvalidOrderStateException;
use App\Events\Order\OrderStatusChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    private array $stateMap = [
        'pending' => PendingOrderState::class,
        'processing' => ProcessingOrderState::class,
        'shipped' => ShippedOrderState::class,
        'delivered' => DeliveredOrderState::class,
        'cancelled' => CancelledOrderState::class,
    ];

    /**
     * Siparişin mevcut durumuna karşılık gelen durum nesnesini döndürür.
     *
     * @param Order $order Sipariş
     * @return OrderStateInterface Durum nesnesi
     */
    public function getOrderState(Order $order): OrderStateInterface
    {
        $stateClass = $this->stateMap[$order->status] ?? null;
        
        if (!$stateClass) {
            throw new InvalidOrderStateException("Unknown status: {$order->status}");
        }
        
        return app($stateClass);
    }

    /**
     * Sipariş durumunu günceller ve gerekli geçiş giriş/çıkış işlemlerini gerçekleştirir.
     *
     * @param Order $order Sipariş
     * @param OrderStatus $newStatus Yeni durum
     * @param User|null $updatedBy Güncelleyen kullanıcı
     * @param string|null $reason Sebep
     * @return void
     */
    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void
    {
        $previousStatus = $order->status;
        
        DB::transaction(function () use ($order, $newStatus, $updatedBy, $reason, $previousStatus) {
            $currentState = $this->getOrderState($order);
            
            // Durumdan çıkışı işle
            $currentState->exit($order);
            
            // Sipariş durumunu güncelle
            $order->update(['status' => $newStatus->value]);
            
            // Durum geçmişini kaydet (DB)
            $this->recordStatusHistoryDb($order, $previousStatus, $newStatus->value, $updatedBy, $reason);
            
            // Yeni duruma giriş işlemlerini yap
            $newState = $this->getOrderState($order);
            $newState->enter($order);
        });

        // Commit sonrası domain etkinliği yayınla ve logla
        DB::afterCommit(function () use ($order, $previousStatus, $newStatus, $updatedBy, $reason) {
            event(new OrderStatusChanged($order, $previousStatus, $newStatus->value, $updatedBy));
            Log::info('Order status updated in service', [
                'order_id' => $order->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus->value,
                'updated_by' => $updatedBy?->id,
                'reason' => $reason
            ]);
        });
    }

    /**
     * Sipariş için başlangıç durumunu ayarlar.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function setInitialStatus(Order $order): void
    {
        $initialStatus = $this->determineInitialStatus($order);

        DB::transaction(function () use ($order, $initialStatus) {
            // Gereksiz durum geçişlerini önlemek için başlangıç durumunda updateStatus kullanılmaz
            $order->update(['status' => $initialStatus->value]);
            
            // Başlangıç durumunu kaydet (DB)
            $this->recordStatusHistoryDb($order, null, $initialStatus->value, null, 'Order created');
            
            // Başlangıç durumuna giriş işlemlerini yap
            $state = $this->getOrderState($order);
            $state->enter($order);
        });
    }

    /**
     * Mevcut durumdan yapılabilecek geçişleri döndürür.
     *
     * @param Order $order Sipariş
     * @return array Geçişler
     */
    public function getAvailableTransitions(Order $order): array
    {
        $currentState = $this->getOrderState($order);
        return $currentState->getAvailableTransitions();
    }

    /**
     * Mevcut durumda yapılabilecek eylemleri döndürür.
     *
     * @param Order $order Sipariş
     * @return array Eylemler
     */
    public function getAvailableActions(Order $order): array
    {
        $currentState = $this->getOrderState($order);
        return $currentState->getAvailableActions();
    }

    /**
     * Belirtilen yeni duruma geçiş yapılıp yapılamayacağını kontrol eder.
     *
     * @param Order $order Sipariş
     * @param OrderStatus $newStatus Yeni durum
     * @return bool Geçiş mümkün mü
     */
    public function canTransitionTo(Order $order, OrderStatus $newStatus): bool
    {
        try {
            $currentState = $this->getOrderState($order);
            return $currentState->canTransitionTo($newStatus);
        } catch (\Exception $e) {
            Log::warning('Error checking status transition', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'target_status' => $newStatus->value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Mevcut sipariş durumunu işler.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function processCurrentState(Order $order): void
    {
        try {
            $currentState = $this->getOrderState($order);
            $currentState->process($order);
        } catch (\Exception $e) {
            Log::error('Error processing order state', [
                'order_id' => $order->id,
                'status' => $order->status,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Başlangıç sipariş durumunu belirler.
     *
     * @param Order $order Sipariş
     * @return OrderStatus Başlangıç durumu
     */
    private function determineInitialStatus(Order $order): OrderStatus
    {
        // Kredi ödemeli B2B siparişler processing olarak başlar
        if ($order->customer_type === 'B2B' && $order->payment_method === 'credit') {
            return OrderStatus::Processing;
        }
        
        // Normal siparişler ödeme onayına kadar pending durumunda başlar
        return OrderStatus::Pending;
    }

    /**
     * (Not: Kullanılmıyor) Durum geçmişini sipariş notlarına yazar.
     *
     * @param Order $order Sipariş
     * @param string|null $previousStatus Önceki durum
     * @param string $newStatus Yeni durum
     * @param User|null $updatedBy Güncelleyen kullanıcı
     * @param string|null $reason Sebep
     * @return void
     */
    private function recordStatusHistory(Order $order, ?string $previousStatus, string $newStatus, ?User $updatedBy, ?string $reason): void
    {
        try {
            // Şimdilik ayrı bir durum geçmişi tablosu olmadığından sipariş notlarına eklenir
            $historyEntry = [
                'timestamp' => now()->toISOString(),
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'updated_by' => $updatedBy?->name ?? 'System',
                'reason' => $reason
            ];
            
            $currentNotes = $order->notes ? $order->notes . "\n" : '';
            $statusNote = sprintf(
                "[%s] Status changed from '%s' to '%s' by %s%s",
                $historyEntry['timestamp'],
                $previousStatus ?? 'none',
                $newStatus,
                $historyEntry['updated_by'],
                $reason ? " (Reason: {$reason})" : ''
            );
            
            $order->update(['notes' => $currentNotes . $statusNote]);
            
        } catch (\Exception $e) {
            Log::error('Failed to record status history', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Durum geçmişini OrderStatusHistory tablosuna yazar.
     *
     * @param Order $order Sipariş
     * @param string|null $previousStatus Önceki durum
     * @param string $newStatus Yeni durum
     * @param User|null $updatedBy Güncelleyen kullanıcı
     * @param string|null $reason Sebep
     * @return void
     */
    private function recordStatusHistoryDb(Order $order, ?string $previousStatus, string $newStatus, ?User $updatedBy, ?string $reason): void
    {
        try {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => $previousStatus,
                'status' => $newStatus,
                'user_id' => $updatedBy?->id,
                'notes' => $reason,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to record status history (db)', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

