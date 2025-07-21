<?php

namespace Tests\Unit\Pricing\ValueObjects;

use App\ValueObjects\Price;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class PriceTest extends TestCase
{
    public function test_can_create_price_with_valid_amount()
    {
        $price = new Price(100.50, 'TRY');
        
        $this->assertEquals(100.50, $price->getAmount());
        $this->assertEquals('TRY', $price->getCurrency());
    }

    public function test_cannot_create_price_with_negative_amount()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Price amount cannot be negative');
        
        new Price(-10.00, 'TRY');
    }

    public function test_cannot_create_price_with_empty_currency()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currency cannot be empty');
        
        new Price(100.00, '');
    }

    public function test_price_addition()
    {
        $price1 = new Price(100.00, 'TRY');
        $price2 = new Price(50.00, 'TRY');
        
        $result = $price1->add($price2);
        
        $this->assertEquals(150.00, $result->getAmount());
        $this->assertEquals('TRY', $result->getCurrency());
    }

    public function test_price_subtraction()
    {
        $price1 = new Price(100.00, 'TRY');
        $price2 = new Price(30.00, 'TRY');
        
        $result = $price1->subtract($price2);
        
        $this->assertEquals(70.00, $result->getAmount());
        $this->assertEquals('TRY', $result->getCurrency());
    }

    public function test_price_multiplication()
    {
        $price = new Price(25.00, 'TRY');
        
        $result = $price->multiply(4);
        
        $this->assertEquals(100.00, $result->getAmount());
    }

    public function test_cannot_add_prices_with_different_currencies()
    {
        $price1 = new Price(100.00, 'TRY');
        $price2 = new Price(50.00, 'USD');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot perform operations on different currencies');
        
        $price1->add($price2);
    }

    public function test_price_comparison()
    {
        $price1 = new Price(100.00, 'TRY');
        $price2 = new Price(50.00, 'TRY');
        $price3 = new Price(100.00, 'TRY');
        
        $this->assertTrue($price1->isGreaterThan($price2));
        $this->assertFalse($price2->isGreaterThan($price1));
        $this->assertTrue($price1->equals($price3));
    }

    public function test_price_formatting()
    {
        $price = new Price(1234.56, 'TRY');
        
        $this->assertEquals('1.234,56 ₺', $price->format());
    }

    public function test_price_to_string()
    {
        $price = new Price(100.00, 'TRY');
        
        $this->assertEquals('100.00 TRY', (string) $price);
    }

    public function test_price_immutability()
    {
        $originalPrice = new Price(100.00, 'TRY');
        $newPrice = $originalPrice->add(new Price(50.00, 'TRY'));
        
        // Original price should not be modified
        $this->assertEquals(100.00, $originalPrice->getAmount());
        $this->assertEquals(150.00, $newPrice->getAmount());
    }
}