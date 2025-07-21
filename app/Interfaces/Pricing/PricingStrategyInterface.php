<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\Models\ProductVariant;
use App\ValueObjects\PriceContext;
use App\ValueObjects\PriceResult;

interface PricingStrategyInterface
{
    public function calculate(ProductVariant $variant, PriceContext $context): PriceResult;
}
