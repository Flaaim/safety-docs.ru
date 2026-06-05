<?php

namespace App\Distribution\Service;

use App\Product\Service\File\DirectoryCreatorInterface;

class DirectoryCreator implements DirectoryCreatorInterface
{
    public function createDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }
        $status = mkdir($path, 0777, true);
        if ($status === false) {
            throw new \DomainException('Unable to create directory ' . $path);
        }
    }
}
