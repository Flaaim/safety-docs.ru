<?php

namespace App\Product\Entity;

use App\Shared\Domain\ValueObject\Currency;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class AmountType extends StringType
{
    public const NAME = 'amount';
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Amount ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Amount
    {
        return !empty($value) ? new Amount((float)$value, new Currency('RUB')) : null;
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