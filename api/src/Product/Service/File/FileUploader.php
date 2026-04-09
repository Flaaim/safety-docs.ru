<?php

namespace App\Product\Service\File;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

class FileUploader implements FileUploaderInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath
    ){
    }
    public function upload(string $relativePathDir, UploadedFileInterface $uploadedFile): void
    {
        if($uploadedFile->getError() !== UPLOAD_ERR_OK){
            throw new \DomainException('Error uploading file '. $uploadedFile->getError());
        }
        $filePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
             . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename();

        $this->createDirectory(dirname($filePath));

        $uploadedFile->moveTo($filePath);
    }

    private function createDirectory(string $path): void
    {
        if(is_dir($path)) {
            return;
        }
        $status = mkdir($path, 0777, true);
        if($status === false){
            throw new \DomainException('Unable to create directory ' . $path);
        }
    }
}