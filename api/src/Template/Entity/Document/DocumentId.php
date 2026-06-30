<?php

declare(strict_types=1);

namespace App\Template\Entity\Document;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final class DocumentId
{
    private string $value;
    public function __construct(string $value)
    {
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

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(DocumentId $directionId): bool
    {
        return $this->value === $directionId->value;
    }
}
