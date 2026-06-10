<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File\DTO;

use App\Distribution\Entity\File\File;

final class FileDTOMapper
{
    public function map(?File $file): ?FileDTO
    {
        if ($file === null) {
            return null;
        }
        return FileDTO::fromFile($file);
    }

    public function mapCollection(array $files): array
    {
        $result = [];
        foreach ($files as $file) {
            $result[] = $this->map($file);
        }
        return $result;
    }
}
