<?php

namespace App\Product\Service\File;

use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;

class FileRemover implements FileRemoverInterface
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath
    ){
    }
    public function remove(string $filePath): void
    {
        $fullPath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $filePath;
        if(!file_exists($fullPath)) {
            return;
        }
        $result = unlink($fullPath);
        if(!$result){
            throw new \DomainException('Error deleting file '. $fullPath);
        }
    }
}