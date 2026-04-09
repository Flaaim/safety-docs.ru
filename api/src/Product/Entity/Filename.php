<?php

namespace App\Product\Entity;

use Webmozart\Assert\Assert;

class Filename
{
    private string $value;
    public function __construct(string $value)
    {
        $value = mb_strtolower($value);
        Assert::regex($value, '/^[a-z0-9]+\.[a-z]{3}$/');
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

}