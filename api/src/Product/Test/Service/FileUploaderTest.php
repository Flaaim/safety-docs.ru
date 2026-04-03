<?php

namespace App\Product\Test\Service;

use App\Product\Service\File\FileUploader;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

class FileUploaderTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    private FileUploader $handler;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create(); // /tmp/phpunit_test_
        $this->handler = new FileUploader();
    }

    public function testErrorUpload(): void
    {
        $uploadFile = $this->createUploadFile('error_file.txt', 'text/plain', 1,UPLOAD_ERR_NO_FILE);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Error uploading file '. $uploadFile->getError());
        $this->handler->upload($this->tempDir->getValue(), $uploadFile);
    }


    public function testUpload(): void
    {
        $uploadFile = $this->createMock(UploadedFileInterface::class);
        $uploadFile->expects(self::once())->method('getClientFilename')->willReturn('text.txt');

        $uploadedDir = $this->tempDir->getValue(). DIRECTORY_SEPARATOR . 'safety/service';

        $uploadFile->expects(self::once())->method('moveTo')
            ->with($this->equalTo($uploadedDir . DIRECTORY_SEPARATOR . 'text.txt'));

        $this->handler->upload($uploadedDir, $uploadFile);

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

    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}