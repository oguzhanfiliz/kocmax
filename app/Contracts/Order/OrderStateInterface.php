<?php

declare(strict_types=1);

namespace App\Contracts\Order;

use App\Models\Order;
use App\Enums\OrderStatus;

interface OrderStateInterface
{
    /**
     * Check if order can transition to new status
     */
    public function canTransitionTo(OrderStatus $newStatus): bool;

    /**
     * Process order in current state
     */
    public function process(Order $order): void;

    /**
     * Get available actions for current state
     */
    public function getAvailableActions(): array;

    /**
     * Get available status transitions
     */
    public function getAvailableTransitions(): array;

    /**
     * Handle entry into this state
     */
    public function enter(Order $order): void;

    /**
     * Handle exit from this state
     */
    public function exit(Order $order): void;
}