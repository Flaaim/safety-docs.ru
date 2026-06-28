<?php

namespace App\Document\Service\File;

use Psr\Http\Message\UploadedFileInterface;

interface FileUploaderInterface
{
    public function upload(string $relativePathDir, UploadedFileInterface $uploadedFile): string;
}
