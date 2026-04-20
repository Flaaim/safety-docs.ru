<?php

namespace App\Product\Test\Service;

use App\Product\Service\File\DirectoryCreator;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\TestCase;


class DirectoryCreatorTest extends TestCase
{
    private InMemoryFileSystemPath $fileSystem;
    public function setUp(): void
    {
        $this->fileSystem = InMemoryFileSystemPath::create();
    }
    public function testSuccess(): void
    {
        $creator = new DirectoryCreator();
        $creator->createDirectory($dir = $this->fileSystem->getValue() .'/one');

        self::assertDirectoryExists($dir);
    }

    public function tearDown(): void
    {
        $this->fileSystem->clear();
    }
}