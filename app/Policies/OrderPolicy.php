<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Users can view their own orders
        if ($user->id === $order->user_id) {
            return true;
        }

        // Admin/manager/customer service can view all orders
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create orders
        return true;
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Only admin and managers can update orders
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update order status.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        // Admin, managers, and customer service can update order status
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Users can cancel their own orders if in cancellable state
        if ($user->id === $order->user_id) {
            return $order->status->canTransitionTo(\App\Enums\OrderStatus::Cancelled);
        }

        // Admin and managers can cancel any order
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can process payment for the order.
     */
    public function processPayment(User $user, Order $order): bool
    {
        // Users can process payment for their own orders
        if ($user->id === $order->user_id) {
            return $order->payment_status !== 'paid';
        }

        // Admin, managers, and customer service can process payments
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admin can delete orders
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the order.
     */
    public function restore(User $user, Order $order): bool
    {
        // Only admin can restore orders
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        // Only admin can force delete orders
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view order analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can export orders.
     */
    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }
}