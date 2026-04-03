<?php

namespace App\Product\Test\Command\Add\Upload;

use App\Product\Command\Upload\Handler;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class HandlerTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    private Handler $handler;
    private FileUploaderInterface $fileUploader;

    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create();
        $this->fileUploader = $this->createMock(FileUploaderInterface::class);
        $this->handler = new Handler($this->tempDir, $this->fileUploader);
    }

    public function testFileExists(): void
    {
        $file = 'safety/service/serv100.rar';
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $this->createExistingFile($file);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('File /tmp/phpunit_test_/safety/service/serv100.rar is exists.');
        $this->handler->handle($file, $uploadedFile);
    }
    public function testSuccess(): void
    {
        $file = 'safety/service/serv100.rar';
        $uploadedFile = $this->createMock(UploadedFileInterface::class);
        $this->fileUploader->expects($this->once())->method('upload')
            ->with(
                $this->equalTo('/tmp/phpunit_test_/safety/service'),
                $this->equalTo($uploadedFile)
            );

        $this->handler->handle($file, $uploadedFile);

        self::assertDirectoryExists('/tmp/phpunit_test_/safety/service');
    }


    private function createExistingFile(string $path): void
    {
        $fullPath = $this->tempDir->getValue() . DIRECTORY_SEPARATOR . $path;
        $result = mkdir(dirname($fullPath), 0777, true);
        if(!$result){
            throw new \RuntimeException('Unable to create temp directory ' . $this->tempDir->getValue() . DIRECTORY_SEPARATOR . $path);
        }
        $file = file_put_contents($fullPath, 'rar');
        if(!$file){
            throw new \RuntimeException('Unable to write file ' . $this->tempDir->getValue() . DIRECTORY_SEPARATOR . $path);
        }
    }

    public function tearDown(): void
    {
        $this->tempDir->clear();
    }

}