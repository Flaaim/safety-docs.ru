<?php

namespace App\Sender\Entity;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailMessageType extends StringType
{
    public const NAME = 'email_message';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof EmailMessage ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailMessage
    {
        return !empty($value) ? new EmailMessage((string)$value) : null;
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