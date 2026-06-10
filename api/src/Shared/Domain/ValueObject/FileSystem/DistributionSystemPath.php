<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\FileSystem;

use Webmozart\Assert\Assert;

final class DistributionSystemPath implements FileSystemPathInterface
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
