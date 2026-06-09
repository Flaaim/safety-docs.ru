<?php

namespace App\Distribution\Service;

interface ContactFileImporterInterface
{
    public function import(string $path): array;
}