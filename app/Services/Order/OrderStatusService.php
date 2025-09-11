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

    public function getOrderState(Order $order): OrderStateInterface
    {
        $stateClass = $this->stateMap[$order->status] ?? null;
        
        if (!$stateClass) {
            throw new InvalidOrderStateException("Unknown status: {$order->status}");
        }
        
        return app($stateClass);
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void
    {
        $previousStatus = $order->status;
        
        DB::transaction(function () use ($order, $newStatus, $updatedBy, $reason, $previousStatus) {
            $currentState = $this->getOrderState($order);
            
            // Handle state exit
            $currentState->exit($order);
            
            // Update order status
            $order->update(['status' => $newStatus->value]);
            
            // Record status history (DB)
            $this->recordStatusHistoryDb($order, $previousStatus, $newStatus->value, $updatedBy, $reason);
            
            // Handle state entry
            $newState = $this->getOrderState($order);
            $newState->enter($order);
        });

        // Emit domain event and log after commit
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

    public function setInitialStatus(Order $order): void
    {
        $initialStatus = $this->determineInitialStatus($order);

        DB::transaction(function () use ($order, $initialStatus) {
            // Don't use updateStatus for initial status to avoid unnecessary state transitions
            $order->update(['status' => $initialStatus->value]);
            
            // Record the initial status (DB)
            $this->recordStatusHistoryDb($order, null, $initialStatus->value, null, 'Order created');
            
            // Handle initial state entry
            $state = $this->getOrderState($order);
            $state->enter($order);
        });
    }

    public function getAvailableTransitions(Order $order): array
    {
        $currentState = $this->getOrderState($order);
        return $currentState->getAvailableTransitions();
    }

    public function getAvailableActions(Order $order): array
    {
        $currentState = $this->getOrderState($order);
        return $currentState->getAvailableActions();
    }

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

    private function determineInitialStatus(Order $order): OrderStatus
    {
        // B2B orders with credit payment start as processing
        if ($order->customer_type === 'B2B' && $order->payment_method === 'credit') {
            return OrderStatus::Processing;
        }
        
        // Regular orders start as pending until payment confirmation
        return OrderStatus::Pending;
    }

    private function recordStatusHistory(Order $order, ?string $previousStatus, string $newStatus, ?User $updatedBy, ?string $reason): void
    {
        try {
            // For now, we'll add this to the order notes since we don't have a separate status history table yet
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
