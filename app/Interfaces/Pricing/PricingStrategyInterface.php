<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\Models\ProductVariant;
use App\ValueObjects\Pricing\PriceContext;
use App\ValueObjects\Pricing\PriceResult;

interface PricingStrategyInterface
{
    public function calculate(ProductVariant $variant, PriceContext $context): PriceResult;
}
