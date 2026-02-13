<?php

namespace App\Payment\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class PaymentStatusType extends StringType
{

    const NAME = 'payment_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof PaymentStatus ? $value->getValue() : $value;
    }
    public function convertToPHPValue($value, AbstractPlatform $platform): ?PaymentStatus
    {
        return !empty($value) ? new PaymentStatus((string)$value) : null;
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