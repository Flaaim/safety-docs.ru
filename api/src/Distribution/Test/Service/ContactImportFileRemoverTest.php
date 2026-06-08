<?php

declare(strict_types=1);

namespace App\Distribution\Test\Service;

use App\Product\Service\File\FileRemover;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;

final class ContactImportFileRemoverTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create(); // /tmp/phpunit_test_
        $this->fileRemover = new FileRemover($this->tempDir);
    }
    public function testFileRemove(): void
    {
        $file = $this->creatTempFile();

        $this->fileRemover->remove($file);

        self::assertFileDoesNotExist(basename($file));
    }

    private function creatTempFile(): string
    {
        return tempnam($this->tempDir->getValue(), 'test');
    }
    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}