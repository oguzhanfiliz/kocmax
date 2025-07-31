<?php

declare(strict_types=1);

namespace App\ValueObjects\Cart;

class CheckoutContext
{
    public function __construct(
        private readonly int $cartId,
        private readonly array $items,
        private readonly CartSummary $summary,
        private readonly string $customerType,
        private readonly array $metadata = []
    ) {}

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSummary(): CartSummary
    {
        return $this->summary;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getTotalAmount(): float
    {
        return $this->summary->getTotal();
    }

    public function getItemCount(): int
    {
        return $this->summary->getItemCount();
    }

    public function getSubtotal(): float
    {
        return $this->summary->getSubtotal();
    }

    public function getDiscount(): float
    {
        return $this->summary->getDiscount();
    }

    public function getAppliedDiscounts(): array
    {
        return $this->summary->getAppliedDiscounts();
    }

    public function isB2B(): bool
    {
        return $this->customerType === 'b2b';
    }

    public function isB2C(): bool
    {
        return $this->customerType === 'b2c';
    }

    public function isGuest(): bool
    {
        return $this->customerType === 'guest';
    }

    public function hasDiscount(): bool
    {
        return $this->summary->hasDiscount();
    }

    public function isEmpty(): bool
    {
        return $this->summary->isEmpty();
    }

    public function getItemsForOrder(): array
    {
        return array_map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['calculated_price'] ?? $item['price'],
                'total_price' => ($item['calculated_price'] ?? $item['price']) * $item['quantity'],
                'discount_amount' => $item['total_discount'] ?? 0,
                'applied_discounts' => $item['applied_discounts'] ?? []
            ];
        }, $this->items);
    }

    public function getPricingSnapshot(): array
    {
        return [
            'customer_type' => $this->customerType,
            'subtotal' => $this->summary->getSubtotal(),
            'total_discount' => $this->summary->getDiscount(),
            'final_total' => $this->summary->getTotal(),
            'applied_discounts' => $this->summary->getAppliedDiscounts(),
            'item_count' => $this->summary->getItemCount(),
            'calculated_at' => now()->toISOString()
        ];
    }

    public function toArray(): array
    {
        return [
            'cart_id' => $this->cartId,
            'items' => $this->items,
            'summary' => $this->summary->toArray(),
            'customer_type' => $this->customerType,
            'metadata' => $this->metadata,
            'pricing_snapshot' => $this->getPricingSnapshot(),
            'items_for_order' => $this->getItemsForOrder()
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        return sprintf(
            'CheckoutContext(cart: %d, items: %d, total: %.2f, type: %s)',
            $this->cartId,
            $this->getItemCount(),
            $this->getTotalAmount(),
            $this->customerType
        );
    }
}