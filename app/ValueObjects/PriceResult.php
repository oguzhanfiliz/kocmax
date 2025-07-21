<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\CustomerType;
use Illuminate\Support\Collection;

class PriceResult
{
    private readonly Price $originalPrice;
    private readonly Price $finalPrice;
    private readonly Collection $appliedDiscounts;
    private readonly Price $totalDiscountAmount;
    private readonly CustomerType $customerType;
    private readonly int $quantity;
    private readonly array $metadata;

    public function __construct(
        Price $originalPrice,
        Price $finalPrice,
        Collection $appliedDiscounts,
        CustomerType $customerType,
        int $quantity = 1,
        array $metadata = []
    ) {
        $this->originalPrice = $originalPrice;
        $this->finalPrice = $finalPrice;
        $this->appliedDiscounts = $appliedDiscounts;
        $this->customerType = $customerType;
        $this->quantity = $quantity;
        $this->metadata = $metadata;

        // Calculate total discount amount
        $this->totalDiscountAmount = $originalPrice->subtract($finalPrice);
    }

    public function getOriginalPrice(): Price
    {
        return $this->originalPrice;
    }

    public function getFinalPrice(): Price
    {
        return $this->finalPrice;
    }

    public function getAppliedDiscounts(): Collection
    {
        return $this->appliedDiscounts;
    }

    public function getTotalDiscountAmount(): Price
    {
        return $this->totalDiscountAmount;
    }

    public function getCustomerType(): CustomerType
    {
        return $this->customerType;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function hasDiscounts(): bool
    {
        return $this->appliedDiscounts->isNotEmpty();
    }

    public function getDiscountCount(): int
    {
        return $this->appliedDiscounts->count();
    }

    public function getTotalDiscountPercentage(): float
    {
        if ($this->originalPrice->isZero()) {
            return 0.0;
        }

        return ($this->totalDiscountAmount->getAmount() / $this->originalPrice->getAmount()) * 100;
    }

    public function getUnitOriginalPrice(): Price
    {
        if ($this->quantity <= 0) {
            return $this->originalPrice;
        }
        
        return new Price(
            $this->originalPrice->getAmount() / $this->quantity,
            $this->originalPrice->getCurrency()
        );
    }

    public function getUnitFinalPrice(): Price
    {
        if ($this->quantity <= 0) {
            return $this->finalPrice;
        }
        
        return new Price(
            $this->finalPrice->getAmount() / $this->quantity,
            $this->finalPrice->getCurrency()
        );
    }

    public function getTotalOriginalPrice(): Price
    {
        return $this->originalPrice->multiply($this->quantity);
    }

    public function getTotalFinalPrice(): Price
    {
        return $this->finalPrice->multiply($this->quantity);
    }

    public function getSavings(): Price
    {
        return $this->totalDiscountAmount;
    }

    public function getSavingsPercentage(): float
    {
        return $this->getTotalDiscountPercentage();
    }

    public function getDiscountsByType(string $type): Collection
    {
        return $this->appliedDiscounts->filter(
            fn(Discount $discount) => $discount->getType() === $type
        );
    }

    public function hasDiscountType(string $type): bool
    {
        return $this->getDiscountsByType($type)->isNotEmpty();
    }

    public function getHighestPriorityDiscount(): ?Discount
    {
        return $this->appliedDiscounts
            ->sortByDesc('priority')
            ->first();
    }

    public function withMetadata(string $key, mixed $value): self
    {
        $newMetadata = array_merge($this->metadata, [$key => $value]);
        
        return new self(
            $this->originalPrice,
            $this->finalPrice,
            $this->appliedDiscounts,
            $this->customerType,
            $this->quantity,
            $newMetadata
        );
    }

    public function toArray(): array
    {
        return [
            'original_price' => $this->originalPrice->toArray(),
            'final_price' => $this->finalPrice->toArray(),
            'unit_original_price' => $this->getUnitOriginalPrice()->toArray(),
            'unit_final_price' => $this->getUnitFinalPrice()->toArray(),
            'total_original_price' => $this->getTotalOriginalPrice()->toArray(),
            'total_final_price' => $this->getTotalFinalPrice()->toArray(),
            'discounts' => $this->appliedDiscounts->map(fn(Discount $discount) => $discount->toArray())->toArray(),
            'total_discount_amount' => $this->totalDiscountAmount->toArray(),
            'savings_percentage' => round($this->getSavingsPercentage(), 2),
            'customer_type' => $this->customerType->value,
            'quantity' => $this->quantity,
            'has_discounts' => $this->hasDiscounts(),
            'discount_count' => $this->getDiscountCount(),
            'metadata' => $this->metadata
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        $discountInfo = '';
        if ($this->hasDiscounts()) {
            $discountInfo = sprintf(' (Save %s - %.1f%%)', 
                $this->totalDiscountAmount->formatForDisplay(),
                $this->getSavingsPercentage()
            );
        }

        return sprintf(
            'Price: %s → %s%s [%s, Qty: %d]',
            $this->originalPrice->formatForDisplay(),
            $this->finalPrice->formatForDisplay(),
            $discountInfo,
            $this->customerType->value,
            $this->quantity
        );
    }
}