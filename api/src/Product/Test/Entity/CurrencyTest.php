<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\Currency;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testSuccess(): void
    {
        $currency = new Currency('RUB');
        $this->assertEquals('RUB', $currency->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Currency('RKZ');
    }

    public function testEmpty(): void
    {
        $currency = new Currency();
        $this->assertEquals('RUB', $currency->getValue());
    }

}