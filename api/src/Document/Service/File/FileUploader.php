<?php

namespace App\Document\Service\File;

use App\Shared\Domain\Service\File\DirectoryCreatorInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

class FileUploader implements FileUploaderInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator,
        private readonly FileNameGeneratorInterface $nameGenerator
    ) {
    }
    public function upload(
        string $relativePathDir,
        UploadedFileInterface $uploadedFile,
    ): string {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new \DomainException('Error uploading file ' . $uploadedFile->getError());
        }

        $filename = $this->nameGenerator->generate($uploadedFile);

        $filePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
             . DIRECTORY_SEPARATOR . $filename;

        $this->directoryCreator->createDirectory(dirname($filePath));

        $uploadedFile->moveTo($filePath);

        return $filename;
    }
}
