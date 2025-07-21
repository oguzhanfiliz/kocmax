<?php

namespace Tests\Unit\Pricing\ValueObjects;

use App\ValueObjects\Discount;
use App\ValueObjects\Price;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class DiscountTest extends TestCase
{
    public function test_can_create_percentage_discount()
    {
        $discount = Discount::percentage(15.0);
        
        $this->assertEquals(15.0, $discount->getValue());
        $this->assertEquals('percentage', $discount->getType());
        $this->assertTrue($discount->isPercentage());
        $this->assertFalse($discount->isFixed());
    }

    public function test_can_create_fixed_amount_discount()
    {
        $discount = Discount::fixedAmount(100.0, 'TRY');
        
        $this->assertEquals(100.0, $discount->getValue());
        $this->assertEquals('TRY', $discount->getCurrency());
        $this->assertEquals('fixed_amount', $discount->getType());
        $this->assertFalse($discount->isPercentage());
        $this->assertTrue($discount->isFixed());
    }

    public function test_cannot_create_invalid_percentage()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Percentage must be between 0 and 100');
        
        Discount::percentage(150.0);
    }

    public function test_cannot_create_negative_percentage()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Percentage must be between 0 and 100');
        
        Discount::percentage(-10.0);
    }

    public function test_cannot_create_negative_fixed_amount()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fixed amount cannot be negative');
        
        Discount::fixedAmount(-50.0, 'TRY');
    }

    public function test_apply_percentage_discount_to_price()
    {
        $discount = Discount::percentage(20.0);
        $originalPrice = new Price(100.0, 'TRY');
        
        $discountedPrice = $discount->apply($originalPrice);
        
        $this->assertEquals(80.0, $discountedPrice->getAmount());
        $this->assertEquals('TRY', $discountedPrice->getCurrency());
    }

    public function test_apply_fixed_amount_discount_to_price()
    {
        $discount = Discount::fixedAmount(25.0, 'TRY');
        $originalPrice = new Price(100.0, 'TRY');
        
        $discountedPrice = $discount->apply($originalPrice);
        
        $this->assertEquals(75.0, $discountedPrice->getAmount());
    }

    public function test_fixed_discount_cannot_exceed_original_price()
    {
        $discount = Discount::fixedAmount(150.0, 'TRY');
        $originalPrice = new Price(100.0, 'TRY');
        
        $discountedPrice = $discount->apply($originalPrice);
        
        // Should not go below zero
        $this->assertEquals(0.0, $discountedPrice->getAmount());
    }

    public function test_calculate_discount_amount_percentage()
    {
        $discount = Discount::percentage(25.0);
        $price = new Price(200.0, 'TRY');
        
        $discountAmount = $discount->calculateDiscountAmount($price);
        
        $this->assertEquals(50.0, $discountAmount->getAmount());
    }

    public function test_calculate_discount_amount_fixed()
    {
        $discount = Discount::fixedAmount(30.0, 'TRY');
        $price = new Price(200.0, 'TRY');
        
        $discountAmount = $discount->calculateDiscountAmount($price);
        
        $this->assertEquals(30.0, $discountAmount->getAmount());
    }

    public function test_discount_with_description()
    {
        $discount = Discount::percentage(10.0, 'Early bird discount');
        
        $this->assertEquals('Early bird discount', $discount->getDescription());
    }

    public function test_discount_toString()
    {
        $percentageDiscount = Discount::percentage(15.0);
        $fixedDiscount = Discount::fixedAmount(50.0, 'TRY');
        
        $this->assertEquals('15.0%', (string) $percentageDiscount);
        $this->assertEquals('50.0 TRY', (string) $fixedDiscount);
    }

    public function test_discount_currency_mismatch_throws_exception()
    {
        $discount = Discount::fixedAmount(50.0, 'USD');
        $price = new Price(100.0, 'TRY');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency mismatch between discount and price');
        
        $discount->apply($price);
    }
}