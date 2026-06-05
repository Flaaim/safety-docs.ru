<?php

namespace App\Distribution\Service;

interface DirectoryCreatorInterface
{
    public function createDirectory(string $path): void;
}
