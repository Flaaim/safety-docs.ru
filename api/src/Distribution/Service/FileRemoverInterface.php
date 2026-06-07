<?php

namespace App\Distribution\Service;

interface FileRemoverInterface
{
    public function remove(string $filePath): void;
}
