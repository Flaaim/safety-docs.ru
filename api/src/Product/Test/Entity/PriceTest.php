<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Currency;
use App\Product\Entity\Price;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function testSuccess(): void
    {
        $price = new Price(150.00, new Currency('RUB'));
        $this->assertEquals(150.00, $price->getValue());
    }
    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Price(0.00, new Currency('RUB'));
    }
    public function testRound(): void
    {
        $price = new Price(150.00000, new Currency('RUB'));
        $this->assertEquals(150.00, $price->getValue());
    }

    public function testEquals(): void
    {
        $price = new Price(150.00, new Currency('RUB'));
        $newPrice = new Price(150.00, new Currency('RUB'));
        $this->assertTrue($price->equals($newPrice));
    }
}