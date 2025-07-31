<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any carts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can view the cart.
     */
    public function view(User $user, Cart $cart): bool
    {
        // Users can view their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin/manager/customer service can view all carts
        return $user->hasRole(['admin', 'manager', 'customer_service']);
    }

    /**
     * Determine whether the user can create carts.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create carts
        return true;
    }

    /**
     * Determine whether the user can update the cart.
     */
    public function update(User $user, Cart $cart): bool
    {
        // Users can update their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can update any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the cart.
     */
    public function delete(User $user, Cart $cart): bool
    {
        // Users can delete their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can delete any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the cart.
     */
    public function restore(User $user, Cart $cart): bool
    {
        // Only admin can restore carts
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the cart.
     */
    public function forceDelete(User $user, Cart $cart): bool
    {
        // Only admin can force delete carts
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can add items to the cart.
     */
    public function addItem(User $user, Cart $cart): bool
    {
        // Users can add items to their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can add items to any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update cart items.
     */
    public function updateItem(User $user, Cart $cart): bool
    {
        // Users can update items in their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can update items in any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can remove items from the cart.
     */
    public function removeItem(User $user, Cart $cart): bool
    {
        // Users can remove items from their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can remove items from any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can clear the cart.
     */
    public function clear(User $user, Cart $cart): bool
    {
        // Users can clear their own cart
        if ($user->id === $cart->user_id) {
            return true;
        }

        // Admin and managers can clear any cart
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can migrate the cart.
     */
    public function migrate(User $user): bool
    {
        // All authenticated users can migrate their guest cart
        return true;
    }

    /**
     * Determine whether the user can view cart analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasRole(['admin', 'manager']);
    }
}