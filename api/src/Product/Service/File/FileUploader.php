<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

class FileUploader implements FileUploaderInterface
{
    public function upload(string $path, UploadedFileInterface $uploadedFile): void
    {
        if($uploadedFile->getError() !== UPLOAD_ERR_OK){
            throw new \DomainException('Error uploading file '. $uploadedFile->getError());
        }
        $filePath = rtrim($path, '/') . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename();

        $uploadedFile->moveTo($filePath);
    }
}