<?php

declare(strict_types=1);

namespace App\ValueObjects\Pricing;

use InvalidArgumentException;

class Discount
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED_AMOUNT = 'fixed_amount';

    private readonly float $value;
    private readonly string $type;
    private readonly string $name;
    private readonly ?string $description;
    private readonly int $priority;

    public function __construct(
        float $value,
        string $type = self::TYPE_PERCENTAGE,
        string $name = 'Discount',
        ?string $description = null,
        int $priority = 0
    ) {
        if ($value < 0) {
            throw new InvalidArgumentException('Discount value cannot be negative');
        }

        if (!in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_FIXED_AMOUNT])) {
            throw new InvalidArgumentException('Invalid discount type');
        }

        if ($type === self::TYPE_PERCENTAGE && $value > 100) {
            throw new InvalidArgumentException('Percentage discount cannot exceed 100%');
        }

        $this->value = $value;
        $this->type = $type;
        $this->name = $name;
        $this->description = $description;
        $this->priority = $priority;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isPercentage(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }

    public function isFixedAmount(): bool
    {
        return $this->type === self::TYPE_FIXED_AMOUNT;
    }

    public function apply(Price $originalPrice): Price
    {
        if ($this->isPercentage()) {
            return $originalPrice->applyDiscount($this->value);
        }

        // Fixed amount discount
        $discountAmount = new Price($this->value, $originalPrice->getCurrency());
        return $originalPrice->subtract($discountAmount);
    }

    public function calculateDiscountAmount(Price $originalPrice): Price
    {
        if ($this->isPercentage()) {
            return $originalPrice->percentage($this->value);
        }

        // Fixed amount discount
        $fixedAmount = new Price($this->value, $originalPrice->getCurrency());
        
        // Don't allow discount to exceed original price
        if ($fixedAmount->isGreaterThan($originalPrice)) {
            return $originalPrice;
        }
        
        return $fixedAmount;
    }

    public function canApplyTo(Price $price, int $quantity = 1): bool
    {
        if ($this->isFixedAmount()) {
            $totalPrice = $price->multiply($quantity);
            $discountAmount = new Price($this->value, $price->getCurrency());
            return $totalPrice->isGreaterThan($discountAmount);
        }

        // Percentage discounts can always be applied
        return true;
    }

    public function format(): string
    {
        if ($this->isPercentage()) {
            return number_format($this->value, 1) . '%';
        }

        return number_format($this->value, 2) . ' TRY';
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'priority' => $this->priority,
            'formatted' => $this->format()
        ];
    }

    public static function percentage(float $percentage, string $name = 'Percentage Discount', ?string $description = null, int $priority = 0): self
    {
        return new self($percentage, self::TYPE_PERCENTAGE, $name, $description, $priority);
    }

    public static function fixedAmount(float $amount, string $name = 'Fixed Amount Discount', ?string $description = null, int $priority = 0): self
    {
        return new self($amount, self::TYPE_FIXED_AMOUNT, $name, $description, $priority);
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->format());
    }
}