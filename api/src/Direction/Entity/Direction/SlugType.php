<?php

namespace App\Direction\Entity\Direction;

use App\Direction\Entity\Slug;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class SlugType extends StringType
{
    public const NAME = 'direction_slug';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Slug ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Slug
    {
        return !empty($value) ? new Slug((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}