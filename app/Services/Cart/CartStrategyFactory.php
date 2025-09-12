<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Contracts\Cart\CartStrategyInterface;
use App\Models\Cart;
use App\Services\Cart\AuthenticatedCartStrategy;
use App\Services\Cart\GuestCartStrategy;

class CartStrategyFactory
{
    /**
     * Sepetin durumuna göre uygun stratejiyi oluşturur.
     *
     * @param Cart $cart Sepet modeli
     * @return CartStrategyInterface Uygun strateji
     */
    public function create(Cart $cart): CartStrategyInterface
    {
        if ($cart->user_id) {
            return app(AuthenticatedCartStrategy::class);
        }
        
        return app(GuestCartStrategy::class);
    }

    /**
     * Kullanıcı bilgisine göre uygun stratejiyi oluşturur.
     *
     * @param int|null $userId Kullanıcı kimliği (opsiyonel)
     * @param string|null $sessionId Oturum kimliği (opsiyonel)
     * @return CartStrategyInterface Uygun strateji
     */
    public function createForUser(?int $userId, ?string $sessionId = null): CartStrategyInterface
    {
        if ($userId) {
            return app(AuthenticatedCartStrategy::class);
        }
        
        return app(GuestCartStrategy::class);
    }
}