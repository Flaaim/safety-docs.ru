<?php

namespace App\Shared\Domain\Service\Template;

use Webmozart\Assert\Assert;

class RootPath
{
    private readonly string $path;
    public function __construct(string $path)
    {
        Assert::notEmpty($path);
        $this->path = rtrim($path, '/');
    }
    public function getValue(): string
    {
        return $this->path;
    }
}
