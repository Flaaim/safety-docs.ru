<?php

namespace App\Shared\Domain\ValueObject\FileSystem;

use Webmozart\Assert\Assert;

class FileSystemPath implements FileSystemPathInterface
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