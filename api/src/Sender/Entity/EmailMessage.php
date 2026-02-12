<?php

namespace App\Sender\Entity;

use Webmozart\Assert\Assert;

class EmailMessage
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