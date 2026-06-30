<?php

namespace App\Template\Service\File;

use Psr\Http\Message\UploadedFileInterface;

interface FileUploaderInterface
{
    public function upload(string $relativePathDir, UploadedFileInterface $uploadedFile): string;
}
