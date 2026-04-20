<?php

namespace App\Product\Test\Service;

use App\Product\Service\File\DirectoryCreatorInterface;
use App\Product\Service\File\FileUploader;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

class FileUploaderTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    private DirectoryCreatorInterface $dirCreator;
    private FileUploader $handler;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create(); // /tmp/phpunit_test_
        $this->dirCreator = $this->createMock(DirectoryCreatorInterface::class);
        $this->handler = new FileUploader($this->tempDir, $this->dirCreator);
    }

    public function testErrorUpload(): void
    {
        $uploadFile = $this->createUploadFile('error_file.txt', 'text/plain', 1,UPLOAD_ERR_NO_FILE);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Error uploading file '. $uploadFile->getError());
        $this->handler->upload('error_file.txt', $uploadFile);
    }


    public function testUpload(): void
    {
        $uploadFile = $this->createMock(UploadedFileInterface::class);
        $uploadFile->expects(self::once())->method('getClientFilename')->willReturn('text.rar');
        
        $expectedPath = '/tmp/phpunit_test_/directory/text.rar';

        $this->dirCreator->expects($this->once())->method('createDirectory');

        $uploadFile->expects(self::once())->method('moveTo')
            ->with($this->equalTo($expectedPath));

        $this->handler->upload('directory', $uploadFile);

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