<?php

namespace App\Product\Entity;

use Webmozart\Assert\Assert;

class File
{
    private string $value;
    public function __construct(string $pathToFile)
    {
        Assert::notEmpty($pathToFile);
        $this->value = ltrim($pathToFile, '/');
    }

    public function getPathToFile(): string
    {
        return $this->value;
    }
}