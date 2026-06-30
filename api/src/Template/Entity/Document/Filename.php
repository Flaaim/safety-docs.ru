<?php

namespace App\Template\Entity\Document;

use Webmozart\Assert\Assert;

class Filename
{
    private string $value;
    public function __construct(string $value)
    {
        $value = mb_strtolower($value);
        Assert::regex($value, '/^[a-z0-9._-]+\.[a-z0-9]+$/i');
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
