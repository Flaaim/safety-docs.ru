<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

class BatchFileUploader
{
    public function __construct(
        private FileUploaderInterface $uploader
    )
    {}

    public function uploadBatch(string $path, UploadedFileInterface ...$uploadedFiles): void
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $this->uploader->upload($path, $uploadedFile);
        }
    }
}