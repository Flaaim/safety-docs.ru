<?php

namespace App\Product\Service;

use Psr\Http\Message\UploadedFileInterface;

class UploadFileHandler
{
    public function handle(string $path, UploadedFileInterface $uploadedFile): array
    {
        if($uploadedFile->getError() !== UPLOAD_ERR_OK){
            throw new \DomainException('Error uploading file '. $uploadedFile->getError());
        }
        $filePath = rtrim($path, '/') . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename();

        $uploadedFile->moveTo($filePath);

        return [
            'name' => $uploadedFile->getClientFilename(),
            'mime_type' => $uploadedFile->getClientMediaType(),
            'size' => $uploadedFile->getSize(),
            'path' => $filePath,
        ];
    }
}