<?php

namespace App\Product\Test\Service;

use App\Product\Service\File\BatchFileUploader;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class BatchFileUploaderTest extends TestCase
{
    private FileUploaderInterface $uploader;
    public function setUp(): void
    {
        $this->uploader = $this->createMock(FileUploaderInterface::class);
    }

    public function testUploadBatch(): void
    {
        $files = [
            $this->createMock(UploadedFileInterface::class),
            $this->createMock(UploadedFileInterface::class),
            $this->createMock(UploadedFileInterface::class),
        ];

        $path = '/test/path/to/file';

        $batchFileUploader = new BatchFileUploader($this->uploader);

        $this->uploader->expects($matcher = self::exactly(3))->method('upload')
            ->with(
                self::equalTo($path),
                self::isInstanceOf(UploadedFileInterface::class),
            );

        $batchFileUploader->uploadBatch($path, ...$files);

    }
}