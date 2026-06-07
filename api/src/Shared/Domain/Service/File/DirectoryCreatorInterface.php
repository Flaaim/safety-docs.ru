<?php

namespace App\Shared\Domain\Service\File;

interface DirectoryCreatorInterface
{
    public function createDirectory(string $path): void;
}
