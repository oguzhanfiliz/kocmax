<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

class Price
{
    private readonly float $amount;
    private readonly string $currency;

    public function __construct(float $amount, string $currency = 'TRY')
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Price amount cannot be negative');
        }

        if (empty($currency)) {
            throw new InvalidArgumentException('Currency cannot be empty');
        }

        $this->amount = round($amount, 2);
        $this->currency = strtoupper($currency);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(Price $other): self
    {
        $this->ensureSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Price $other): self
    {
        $this->ensureSameCurrency($other);
        $newAmount = $this->amount - $other->amount;
        
        if ($newAmount < 0) {
            $newAmount = 0;
        }
        
        return new self($newAmount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }
        
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function percentage(float $percentage): self
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Percentage must be between 0 and 100');
        }
        
        return new self($this->amount * ($percentage / 100), $this->currency);
    }

    public function applyDiscount(float $discountPercentage): self
    {
        if ($discountPercentage < 0 || $discountPercentage > 100) {
            throw new InvalidArgumentException('Discount percentage must be between 0 and 100');
        }
        
        $discountAmount = $this->amount * ($discountPercentage / 100);
        return new self($this->amount - $discountAmount, $this->currency);
    }

    public function equals(Price $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function isGreaterThan(Price $other): bool
    {
        $this->ensureSameCurrency($other);
        return $this->amount > $other->amount;
    }

    public function isLessThan(Price $other): bool
    {
        $this->ensureSameCurrency($other);
        return $this->amount < $other->amount;
    }

    public function isZero(): bool
    {
        return $this->amount === 0.0;
    }

    public function format(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function formatForDisplay(): string
    {
        $symbol = $this->getCurrencySymbol();
        return $symbol . number_format($this->amount, 2);
    }

    private function getCurrencySymbol(): string
    {
        return match($this->currency) {
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency . ' '
        };
    }

    private function ensureSameCurrency(Price $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                sprintf('Cannot operate on different currencies: %s and %s', $this->currency, $other->currency)
            );
        }
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted' => $this->format(),
            'display' => $this->formatForDisplay()
        ];
    }

    public function __toString(): string
    {
        return $this->format();
    }
}