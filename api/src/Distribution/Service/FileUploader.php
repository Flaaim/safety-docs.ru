<?php

declare(strict_types=1);

namespace App\Distribution\Service;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

final class FileUploader implements FileUploaderInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly DirectoryCreatorInterface $directoryCreator
    ) {
    }
    public function upload(string $relativePathDir, UploadedFileInterface $uploadedFile): void
    {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new \DomainException('Error uploading file ' . $uploadedFile->getError());
        }
        $filename = $uploadedFile->getClientFilename();

        if ($filename === null) {
            throw new \DomainException('File name cannot be null');
        }

        $filePath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $relativePathDir
            . DIRECTORY_SEPARATOR . $filename;


        $this->directoryCreator->createDirectory(dirname($filePath));

        $uploadedFile->moveTo($filePath);
    }
}
