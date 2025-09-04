<?php

declare(strict_types=1);

namespace App\ValueObjects\Checkout;

use App\Models\Order;
use App\ValueObjects\Pricing\ComprehensivePricingResult;

/**
 * Güvenli checkout oturumu value object'i
 * Frontend manipülasyonuna karşı backend'de saklanan checkout bilgileri
 */
class CheckoutSession
{
    public function __construct(
        private string $sessionId,
        private Order $pendingOrder,
        private ComprehensivePricingResult $pricingResult,
        private \DateTime $expiresAt
    ) {}

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getPendingOrder(): Order
    {
        return $this->pendingOrder;
    }

    public function getPricingResult(): ComprehensivePricingResult
    {
        return $this->pricingResult;
    }

    public function getTotalAmount(): float
    {
        return $this->pricingResult->getFinalTotalPrice();
    }

    public function getTotalDiscount(): float
    {
        return $this->pricingResult->getTotalDiscount();
    }

    public function getAppliedDiscounts(): array
    {
        return $this->pricingResult->getAppliedDiscounts();
    }

    public function getItemDetails(): array
    {
        return $this->pricingResult->getItemResults();
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTime();
    }

    public function getTimeToExpiry(): int
    {
        $now = new \DateTime();
        return max(0, $this->expiresAt->getTimestamp() - $now->getTimestamp());
    }

    /**
     * Frontend için safe data (hassas bilgiler olmadan)
     */
    public function toArray(): array
    {
        return [
            'checkout_session_id' => $this->sessionId,
            'total_amount' => $this->getTotalAmount(),
            'subtotal' => $this->pricingResult->getSubtotal(),
            'total_discount' => $this->getTotalDiscount(),
            'currency' => $this->pendingOrder->currency_code ?? 'TRY',
            'items_count' => count($this->getItemDetails()),
            'applied_discounts' => $this->getAppliedDiscounts(),
            'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
            'time_to_expiry_seconds' => $this->getTimeToExpiry(),
            'is_expired' => $this->isExpired()
        ];
    }

    /**
     * Detaylı item breakdown (frontend için)
     */
    public function getItemBreakdown(): array
    {
        $breakdown = [];
        
        foreach ($this->getItemDetails() as $itemResult) {
            $breakdown[] = [
                'product_variant_id' => $itemResult->getVariantId(),
                'product_name' => $itemResult->getProductName(),
                'quantity' => $itemResult->getQuantity(),
                'unit_price' => $itemResult->getUnitPrice(),
                'total_price' => $itemResult->getTotalPrice(),
                'discounts' => $itemResult->getAppliedDiscounts(),
                'discount_amount' => $itemResult->getTotalDiscount()
            ];
        }
        
        return $breakdown;
    }
}