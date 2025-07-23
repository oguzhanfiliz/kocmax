<?php

declare(strict_types=1);

namespace App\ValueObjects\Campaign;

use Illuminate\Support\Collection;

class CampaignResult
{
    public function __construct(
        private readonly bool $applied,
        private readonly Collection $freeItems = new Collection(),
        private readonly float $discountAmount = 0.0,
        private readonly string $description = '',
        private readonly array $metadata = []
    ) {}

    public function isApplied(): bool
    {
        return $this->applied;
    }

    public function getFreeItems(): Collection
    {
        return $this->freeItems;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function hasFreeItems(): bool
    {
        return $this->freeItems->isNotEmpty();
    }

    public function hasDiscount(): bool
    {
        return $this->discountAmount > 0;
    }

    public function getTotalBenefit(): float
    {
        $freeItemsValue = $this->freeItems->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        });

        return $freeItemsValue + $this->discountAmount;
    }

    public function toArray(): array
    {
        return [
            'applied' => $this->applied,
            'free_items' => $this->freeItems->toArray(),
            'discount_amount' => $this->discountAmount,
            'description' => $this->description,
            'total_benefit' => $this->getTotalBenefit(),
            'metadata' => $this->metadata
        ];
    }

    public static function notApplied(string $reason = ''): self
    {
        return new self(
            applied: false,
            description: $reason
        );
    }

    public static function withFreeItems(Collection $items, string $description = ''): self
    {
        return new self(
            applied: true,
            freeItems: $items,
            description: $description
        );
    }

    public static function withDiscount(float $amount, string $description = ''): self
    {
        return new self(
            applied: true,
            discountAmount: $amount,
            description: $description
        );
    }

    public static function withBoth(Collection $freeItems, float $discountAmount, string $description = ''): self
    {
        return new self(
            applied: true,
            freeItems: $freeItems,
            discountAmount: $discountAmount,
            description: $description
        );
    }
}