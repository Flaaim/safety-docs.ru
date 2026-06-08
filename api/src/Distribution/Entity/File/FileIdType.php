<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class FileIdType extends StringType
{
    public const  NAME = 'file_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof FileId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FileId
    {
        return !empty($value) ? new FileId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}