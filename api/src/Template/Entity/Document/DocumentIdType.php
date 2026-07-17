<?php

declare(strict_types=1);

namespace App\Template\Entity\Document;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class DocumentIdType extends StringType
{
    public const NAME = 'document_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof DocumentId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DocumentId
    {
        return !empty($value) ? new DocumentId((string)$value) : null;
    }
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;
        $column['fixed'] = true; // CHAR вместо VARCHAR

        return $platform->getStringTypeDeclarationSQL($column);
    }
    public function getName(): string
    {
        return self::NAME;
    }
}
