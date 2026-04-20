<?php

namespace App\Product\Service\File;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

class FileUploader implements FileUploaderInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator
    ){
    }
    public function upload(string $relativePathDir, UploadedFileInterface $uploadedFile): void
    {
        if($uploadedFile->getError() !== UPLOAD_ERR_OK){
            throw new \DomainException('Error uploading file '. $uploadedFile->getError());
        }
        $filePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
             . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename();

        $this->directoryCreator->createDirectory(dirname($filePath));

        $uploadedFile->moveTo($filePath);
    }

}