<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

interface FileNameGeneratorInterface
{
    public function generate(UploadedFileInterface $file): string;
}