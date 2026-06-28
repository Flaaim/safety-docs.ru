<?php

declare(strict_types=1);

namespace App\Document\Test\Unit\Service;

use App\Document\Service\File\FileNameGeneratorInterface;
use App\Document\Service\File\FileUploader;
use App\Shared\Domain\Service\File\DirectoryCreatorInterface;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

final class FileUploaderTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    private DirectoryCreatorInterface $dirCreator;
    private FileNameGeneratorInterface $fileNameGenerator;
    private FileUploader $handler;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create(); // /tmp/phpunit_test_
        $this->dirCreator = $this->createMock(DirectoryCreatorInterface::class);

        $this->fileNameGenerator = $this->createMock(FileNameGeneratorInterface::class);
        $this->handler = new FileUploader($this->tempDir, $this->dirCreator, $this->fileNameGenerator);
    }

    public function testUpload(): void
    {
        $uploadFile = $this->createMock(UploadedFileInterface::class);
        $this->fileNameGenerator->expects(self::once())->method('generate')->willReturn('some_string.docx');

        $expectedPath = 'vfs://storage/directory/some_string.docx';

        $this->dirCreator->expects($this->once())->method('createDirectory');

        $uploadFile->expects(self::once())->method('moveTo')
            ->with($this->equalTo($expectedPath));

        $this->handler->upload('directory', $uploadFile);
    }

    public function testErrNotFile(): void
    {
        $uploadFile = $this->createUploadFile('error_file.txt', 'text/plain', 1, UPLOAD_ERR_NO_FILE);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Error uploading file ' . $uploadFile->getError());
        $this->handler->upload('dir', $uploadFile);

    }
    private function createUploadFile(
        string $name = 'test.txt',
        string $type = 'text/plain',
        int $size = 1,
        int $error = UPLOAD_ERR_OK
    ): UploadedFileInterface {
        return new UploadedFile(
            'some_content',
            $name,
            $type,
            $size,
            $error,
        );
    }

    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}