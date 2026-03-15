<?php

namespace App\Category\Entity;

use Webmozart\Assert\Assert;

class Slug
{
    private string $value;
    public function __construct(string $value){
        $value = mb_strtolower($value);
        Assert::regex($value, '/^[a-z0-9]+(?:-[a-z0-9]+)*$/');
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Slug $slug): bool
    {
        return $this->value === $slug->value;
    }
}