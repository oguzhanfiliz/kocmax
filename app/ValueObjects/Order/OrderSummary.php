<?php

declare(strict_types=1);

namespace App\ValueObjects\Order;

class OrderSummary
{
    public function __construct(
        private readonly float $subtotal,
        private readonly float $taxAmount,
        private readonly float $shippingAmount,
        private readonly float $discountAmount,
        private readonly float $totalAmount,
        private readonly int $itemCount,
        private readonly array $itemDetails = [],
        private readonly string $currency = 'TRY'
    ) {}

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function getShippingAmount(): float
    {
        return $this->shippingAmount;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    public function getItemDetails(): array
    {
        return $this->itemDetails;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->totalAmount, 2) . ' ' . $this->currency;
    }

    public function getFormattedSubtotal(): string
    {
        return number_format($this->subtotal, 2) . ' ' . $this->currency;
    }

    public function hasDiscount(): bool
    {
        return $this->discountAmount > 0;
    }

    public function hasShipping(): bool
    {
        return $this->shippingAmount > 0;
    }

    public function hasTax(): bool
    {
        return $this->taxAmount > 0;
    }

    public function getDiscountPercentage(): float
    {
        if ($this->subtotal <= 0) {
            return 0.0;
        }
        
        return ($this->discountAmount / $this->subtotal) * 100;
    }

    public function getTaxPercentage(): float
    {
        if ($this->subtotal <= 0) {
            return 0.0;
        }
        
        return ($this->taxAmount / $this->subtotal) * 100;
    }

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'shipping_amount' => $this->shippingAmount,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->totalAmount,
            'item_count' => $this->itemCount,
            'currency' => $this->currency,
            'formatted_total' => $this->getFormattedTotal(),
            'formatted_subtotal' => $this->getFormattedSubtotal(),
            'has_discount' => $this->hasDiscount(),
            'has_shipping' => $this->hasShipping(),
            'has_tax' => $this->hasTax(),
            'discount_percentage' => $this->getDiscountPercentage(),
            'tax_percentage' => $this->getTaxPercentage(),
            'item_details' => $this->itemDetails
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        return sprintf(
            'OrderSummary(items: %d, subtotal: %.2f, total: %.2f %s)',
            $this->itemCount,
            $this->subtotal,
            $this->totalAmount,
            $this->currency
        );
    }
}