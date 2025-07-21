<?php

declare(strict_types=1);

namespace App\Enums\Pricing;

enum CustomerType: string
{
    case B2B = 'b2b';
    case B2C = 'b2c';
    case GUEST = 'guest';
    case WHOLESALE = 'wholesale';
    case RETAIL = 'retail';

    public function getLabel(): string
    {
        return match($this) {
            self::B2B => 'Business to Business',
            self::B2C => 'Business to Consumer',
            self::GUEST => 'Guest Customer',
            self::WHOLESALE => 'Wholesale Customer',
            self::RETAIL => 'Retail Customer',
        };
    }

    public function isB2B(): bool
    {
        return in_array($this, [self::B2B, self::WHOLESALE]);
    }

    public function isB2C(): bool
    {
        return in_array($this, [self::B2C, self::RETAIL, self::GUEST]);
    }

    public function canAccessDealerPrices(): bool
    {
        return $this->isB2B();
    }

    public function getDefaultDiscountPercentage(): float
    {
        return match($this) {
            self::B2B => 0.0,
            self::WHOLESALE => 5.0,
            self::B2C, self::RETAIL => 0.0,
            self::GUEST => 0.0,
        };
    }
}