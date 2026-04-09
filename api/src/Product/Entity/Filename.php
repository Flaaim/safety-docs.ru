<?php

namespace App\Product\Entity;

use Webmozart\Assert\Assert;

class Filename
{
    private string $value;
    public function __construct(string $value)
    {
        $value = mb_strtolower($value);
        Assert::regex($value, '/^[a-z0-9]+\.[0-9]{1,2}\.[a-z]{2,4}$/');
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    public function __toString(): string
    {
        return $this->value;
    }

}