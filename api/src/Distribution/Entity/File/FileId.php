<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class FileId
{
    public function __construct(
        private string $value,
    ) {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }
    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
    public function __toString(): string
    {
        return $this->value;
    }
}
