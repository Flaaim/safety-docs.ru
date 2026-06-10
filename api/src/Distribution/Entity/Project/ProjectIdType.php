<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project;

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class ProjectIdType extends StringType
{
    public const  NAME = 'project_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof ProjectId ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProjectId
    {
        return !empty($value) ? new ProjectId((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
