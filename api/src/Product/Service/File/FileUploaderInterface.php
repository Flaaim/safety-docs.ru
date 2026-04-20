<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

interface FileUploaderInterface
{
    public function upload(
        string $relativePathDir,
        UploadedFileInterface $uploadedFile,
        ?FileNameGeneratorInterface $nameGenerator = null
    ): string;
}