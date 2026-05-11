<?php

namespace App\Shared\Domain\ValueObject;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

class UpdatedAtType extends DateTimeImmutableType
{
    public const NAME = 'updated_at';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof UpdatedAt ? $value->format('Y-m-d H:i:s') : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        return !empty($value) ? new DateTimeImmutable((string)$value) : null;
    }
    public function getName(): string
    {
        return self::NAME;
    }
}
