<?php

namespace App\Product\Entity;

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
class SlugType extends StringType
{
    public const NAME = 'product_slug';

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