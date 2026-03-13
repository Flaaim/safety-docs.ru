<?php

namespace App\Direction\Entity;

use Webmozart\Assert\Assert;

class Slug
{
    private string $value;
    public function __construct(string $value){
        Assert::regex($value, '/^[a-z0-9]+(?:-[a-z0-9]+)*$/');
        $this->value = $value;
    }
    public function getValue(): string
    {
        return $this->value;
    }
}