<?php

namespace App\Distribution\Service;

use Psr\Http\Message\UploadedFileInterface;

interface ContactImportFileUploaderInterface
{
    public function upload(
        string $relativePathDir,
        UploadedFileInterface $uploadedFile,
    ): void;
}
