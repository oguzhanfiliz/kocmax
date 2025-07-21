<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\ValueObjects\Pricing\PriceResult;

interface PriceDecoratorInterface
{
    public function decorate(PriceResult $priceResult): PriceResult;
}
