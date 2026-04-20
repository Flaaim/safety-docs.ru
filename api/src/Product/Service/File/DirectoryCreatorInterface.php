<?php

namespace App\Product\Service\File;

interface DirectoryCreatorInterface
{
    public function createDirectory(string $path): void;
}