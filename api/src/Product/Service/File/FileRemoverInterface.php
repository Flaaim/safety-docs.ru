<?php

namespace App\Product\Service\File;

interface FileRemoverInterface
{
    public function remove(string $filePath): void;
}