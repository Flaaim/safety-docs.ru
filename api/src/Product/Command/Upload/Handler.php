<?php

namespace App\Product\Command\Upload;

use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

class Handler
{
    public function __construct(
        private readonly FileSystemPathInterface $fileSystemPath,
        private readonly FileUploaderInterface   $fileUploader,
    ){
    }

    /**
     * @param string $file
     * @param UploadedFileInterface $uploadFile
     */
    public function handle(string $file, UploadedFileInterface $uploadFile): void
    {
        $fullPath = $this->fileSystemPath->getValue() . DIRECTORY_SEPARATOR . $file;

        if(file_exists($fullPath)) {
            throw new \DomainException('File '. $fullPath .' is exists.');
        }

        $this->createDirectory(dirname($fullPath));

        $this->fileUploader->upload(dirname($fullPath), $uploadFile);

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