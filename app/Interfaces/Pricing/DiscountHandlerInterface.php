<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\ValueObjects\Pricing\PriceContext;
use App\ValueObjects\Pricing\PriceResult;

interface DiscountHandlerInterface
{
    public function setNext(DiscountHandlerInterface $handler): DiscountHandlerInterface;

    public function handle(PriceResult $priceResult, PriceContext $context): PriceResult;
}
