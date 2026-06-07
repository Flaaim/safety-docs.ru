<?php

namespace App\Distribution\Service;

interface ContactImportFileRemoverInterface
{
    public function remove(string $filePath): void;
}
