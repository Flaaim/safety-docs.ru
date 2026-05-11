<?php

namespace App\Shared\Domain\ValueObject;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class IdType extends StringType
{
    public const NAME = 'id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Id
    {
        return !empty($value) ? new Id((string)$value) : null;
    }
    public function getName(): string
    {
        return self::NAME;
    }
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;
        $column['fixed'] = true; // CHAR вместо VARCHAR

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
