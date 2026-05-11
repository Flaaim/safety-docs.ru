<?php

namespace App\Payment\Entity;

use Webmozart\Assert\Assert;

class Email
{
    private string $value;
    public function __construct(string $value)
    {
        Assert::email($value);
        $this->value = mb_strtolower($value);
    }
    public function getValue(): string
    {
        return $this->value;
    }
}
