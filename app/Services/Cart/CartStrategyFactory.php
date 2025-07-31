<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Services\Cart\AuthenticatedCartStrategy;
use App\Services\Cart\GuestCartStrategy;

class CartStrategyFactory
{
    public function create(Cart $cart): CartStrategyInterface
    {
        if ($cart->user_id) {
            return app(AuthenticatedCartStrategy::class);
        }
        
        return app(GuestCartStrategy::class);
    }

    public function createForUser(?int $userId, ?string $sessionId = null): CartStrategyInterface
    {
        if ($userId) {
            return app(AuthenticatedCartStrategy::class);
        }
        
        return app(GuestCartStrategy::class);
    }
}