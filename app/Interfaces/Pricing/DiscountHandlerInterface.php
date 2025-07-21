<?php

declare(strict_types=1);

namespace App\Interfaces\Pricing;

use App\ValueObjects\PriceContext;
use App\ValueObjects\PriceResult;

interface DiscountHandlerInterface
{
    public function setNext(DiscountHandlerInterface $handler): DiscountHandlerInterface;

    public function handle(PriceResult $priceResult, PriceContext $context): PriceResult;
}
