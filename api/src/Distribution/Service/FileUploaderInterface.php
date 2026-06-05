<?php

namespace App\Distribution\Service;

use Psr\Http\Message\UploadedFileInterface;

interface FileUploaderInterface
{
    public function upload(
        string $relativePathDir,
        UploadedFileInterface $uploadedFile,
    ): void;
}
