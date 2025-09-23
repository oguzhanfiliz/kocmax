<?php

declare(strict_types=1);

namespace App\ValueObjects\Cart;

class CartSummary
{
    private readonly array $itemDetails;
    private readonly array $appliedDiscounts;

    public function __construct(
        private readonly float $subtotal,
        private readonly float $discount,
        private readonly float $total,
        private readonly int $itemCount,
        array $itemDetails = [],
        array $appliedDiscounts = []
    ) {
        $this->itemDetails = $itemDetails;
        $this->appliedDiscounts = $this->normalizeAppliedDiscounts($appliedDiscounts);
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    public function getItemDetails(): array
    {
        return $this->itemDetails;
    }

    public function getAppliedDiscounts(): array
    {
        return $this->appliedDiscounts;
    }

    public function isEmpty(): bool
    {
        return $this->itemCount === 0;
    }

    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    public function getDiscountPercentage(): float
    {
        if ($this->subtotal <= 0) {
            return 0;
        }
        
        return ($this->discount / $this->subtotal) * 100;
    }

    public function getSavings(): float
    {
        return $this->discount;
    }

    public function getTotalWithoutDiscount(): float
    {
        return $this->subtotal;
    }

    public function getDiscountsByType(): array
    {
        $discountsByType = [];
        
        foreach ($this->appliedDiscounts as $discount) {
            $type = $discount['type'] ?? 'unknown';
            
            if (!isset($discountsByType[$type])) {
                $discountsByType[$type] = [
                    'total_amount' => 0,
                    'count' => 0,
                    'discounts' => []
                ];
            }
            
            $discountsByType[$type]['total_amount'] += $discount['amount'] ?? 0;
            $discountsByType[$type]['count']++;
            $discountsByType[$type]['discounts'][] = $discount;
        }
        
        return $discountsByType;
    }

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'item_count' => $this->itemCount,
            'item_details' => $this->itemDetails,
            'applied_discounts' => $this->appliedDiscounts,
            'is_empty' => $this->isEmpty(),
            'has_discount' => $this->hasDiscount(),
            'discount_percentage' => $this->getDiscountPercentage(),
            'savings' => $this->getSavings(),
            'discounts_by_type' => $this->getDiscountsByType()
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        return sprintf(
            'CartSummary(items: %d, subtotal: %.2f, discount: %.2f, total: %.2f)',
            $this->itemCount,
            $this->subtotal,
            $this->discount,
            $this->total
        );
    }

    /**
     * Merge duplicate discounts that target the same price type/context.
     */
    private function normalizeAppliedDiscounts(array $discounts): array
    {
        if ($discounts === []) {
            return [];
        }

        $aggregated = [];

        foreach ($discounts as $discount) {
            if (!is_array($discount)) {
                continue;
            }

            $normalizedType = strtolower((string) ($discount['type'] ?? 'unknown'));
            $identifierParts = [$normalizedType];

            if (isset($discount['id'])) {
                $identifierParts[] = (string) $discount['id'];
            }

            if (isset($discount['code'])) {
                $identifierParts[] = strtolower((string) $discount['code']);
            } elseif (isset($discount['description'])) {
                $identifierParts[] = strtolower((string) $discount['description']);
            }

            $key = implode('|', $identifierParts);
            $amount = (float) ($discount['amount'] ?? 0.0);

            if (!isset($aggregated[$key])) {
                $clean = $discount;
                $clean['type'] = $normalizedType;
                $clean['amount'] = $amount;
                $aggregated[$key] = $clean;
                continue;
            }

            $aggregated[$key]['amount'] = round($aggregated[$key]['amount'] + $amount, 2);
        }

        return array_values($aggregated);
    }
}
