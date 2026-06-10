<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File\DTO;

use App\Distribution\Entity\File\File;

final class FileDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $date,
        public bool $complete,
    ) {
    }

    public static function fromFile(File $file): self
    {
        return new self(
            $file->getId()->getValue(),
            $file->getName(),
            $file->getDate()->format('Y-m-d'),
            $file->isComplete(),
        );
    }
}
