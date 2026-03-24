<?php

namespace App\Product\Test\Service;

use App\Product\Service\UploadFileHandler;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

class UploadFileHandlerTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    private UploadFileHandler $handler;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create(); // /tmp/phpunit_test_
        $this->handler = new UploadFileHandler();
    }

    public function testErrorUpload(): void
    {
        $uploadFile = $this->createUploadFile('error_file.txt', 'text/plain', 1,UPLOAD_ERR_NO_FILE);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Error uploading file '. $uploadFile->getError());
        $this->handler->handle($this->tempDir->getValue(), $uploadFile);
    }


    public function testUpload(): void
    {
        $uploadFile = $this->createMock(UploadedFileInterface::class);
        $uploadFile->expects(self::exactly(2))->method('getClientFilename')->willReturn('text.txt');

        $uploadedDir = $this->tempDir->getValue(). DIRECTORY_SEPARATOR . 'safety/service';

        $this->createDirectory($uploadedDir);
        $result = $this->handler->handle($uploadedDir, $uploadFile);

        self::assertEquals('/tmp/phpunit_test_/safety/service/text.txt', $result['path']);
    }

    private function createUploadFile(string $name = 'test.txt', string $type = 'text/plain', int $size = 1, int $error = UPLOAD_ERR_OK): UploadedFileInterface
    {
        $uploadFile = new UploadedFile(
            'some_content',
            $name,
            $type,
            $size,
            $error,
        );

        return $uploadFile;
    }


    private function createDirectory(string $path): void
    {
        $result = mkdir($path, 0777, true);
        if(!$result){
            throw new \RuntimeException('Unable to create temp directory ' . $path);
        }
    }
    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}