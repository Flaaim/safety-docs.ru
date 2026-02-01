<?php

namespace App\Product\Entity;

use Webmozart\Assert\Assert;

class Currency
{
    const ALLOWED_CURRENCIES = [
        'RUB'
    ];
    private string $value;
    public function __construct(string $value = 'RUB')
    {
        Assert::oneOf($value, self::ALLOWED_CURRENCIES);
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
}