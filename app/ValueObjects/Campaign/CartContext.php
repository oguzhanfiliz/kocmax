<?php

declare(strict_types=1);

namespace App\ValueObjects\Campaign;

use Illuminate\Support\Collection;

class CartContext
{
    public function __construct(
        private readonly Collection $items, // Cart items with product, variant, quantity
        private readonly float $totalAmount,
        private readonly string $customerType = 'guest',
        private readonly ?int $customerId = null,
        private readonly array $metadata = []
    ) {}

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getItemsByProduct(int $productId): Collection
    {
        return $this->items->filter(function ($item) use ($productId) {
            return $item['product_id'] === $productId;
        });
    }

    public function getTotalQuantity(): int
    {
        return $this->items->sum('quantity');
    }

    public function getTotalQuantityForProduct(int $productId): int
    {
        return $this->getItemsByProduct($productId)->sum('quantity');
    }

    public function hasProduct(int $productId): bool
    {
        return $this->items->contains(function ($item) use ($productId) {
            return $item['product_id'] === $productId;
        });
    }

    public function hasProducts(array $productIds): bool
    {
        foreach ($productIds as $productId) {
            if (!$this->hasProduct($productId)) {
                return false;
            }
        }
        return true;
    }

    public function getProductIds(): array
    {
        return $this->items->pluck('product_id')->unique()->toArray();
    }

    public function getItemsInCategory(int $categoryId): Collection
    {
        return $this->items->filter(function ($item) use ($categoryId) {
            return in_array($categoryId, $item['category_ids'] ?? []);
        });
    }

    public function withMetadata(string $key, mixed $value): self
    {
        $metadata = array_merge($this->metadata, [$key => $value]);
        
        return new self(
            $this->items,
            $this->totalAmount,
            $this->customerType,
            $this->customerId,
            $metadata
        );
    }

    /**
     * Factory method for easy creation from cart data
     */
    public static function fromCart(array $cartData): self
    {
        $items = collect($cartData['items'] ?? []);
        $totalAmount = (float) ($cartData['total'] ?? 0);
        $customerType = $cartData['customer_type'] ?? 'guest';
        $customerId = $cartData['customer_id'] ?? null;
        
        return new self($items, $totalAmount, $customerType, $customerId, $cartData);
    }

    /**
     * Factory method for creating from array of items
     */
    public static function fromItems(array $items, float $totalAmount, string $customerType = 'guest', ?int $customerId = null, array $metadata = []): self
    {
        return new self(collect($items), $totalAmount, $customerType, $customerId, $metadata);
    }

    /**
     * Convert to array for logging/debugging
     */
    public function toArray(): array
    {
        return [
            'items' => $this->items->toArray(),
            'total_amount' => $this->totalAmount,
            'customer_type' => $this->customerType,
            'customer_id' => $this->customerId,
            'metadata' => $this->metadata,
        ];
    }
}