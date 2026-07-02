<?php

namespace App\Template\Entity\Document;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class FilenameType extends StringType
{
    public const NAME = 'filename';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Filename ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Filename
    {
        return !empty($value) ? new Filename((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
