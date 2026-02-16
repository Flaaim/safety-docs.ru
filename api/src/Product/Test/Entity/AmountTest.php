<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Amount;
use App\Shared\Domain\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    public function testSuccess(): void
    {
        $price = new Amount(150.00, new Currency('RUB'));
        $this->assertEquals(150.00, $price->getValue());
    }
    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Amount(0.00, new Currency('RUB'));
    }
    public function testRound(): void
    {
        $price = new Amount(150.00000, new Currency('RUB'));
        $this->assertEquals(150.00, $price->getValue());
    }

    public function testEquals(): void
    {
        $price = new Amount(150.00, new Currency('RUB'));
        $newPrice = new Amount(150.00, new Currency('RUB'));
        $this->assertTrue($price->equals($newPrice));
    }
}