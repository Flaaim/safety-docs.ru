<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class NewsletterIdType extends StringType
{
    public const NAME = 'newsletter_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof NewsletterId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?NewsletterId
    {
        return !empty($value) ? new NewsletterId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }


}