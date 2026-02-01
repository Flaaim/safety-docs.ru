<?php

namespace App\Shared\Domain\Service\Template;

use Webmozart\Assert\Assert;

class TemplatePath
{
    private readonly string $basePath;
    public function __construct(string $basePath)
    {
        Assert::notEmpty($basePath);
        $this->basePath = $basePath;
    }
    public function getValue(): string
    {
        return $this->basePath;
    }
}