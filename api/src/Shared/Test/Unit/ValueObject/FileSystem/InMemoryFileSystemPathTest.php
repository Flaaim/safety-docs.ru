<?php

namespace App\Shared\Test\Unit\ValueObject\FileSystem;

use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InMemoryFileSystemPath::class)]
class InMemoryFileSystemPathTest extends TestCase
{
    public function testSuccess(): void
    {
        $temp = InMemoryFileSystemPath::create();

        self::assertEquals('/tmp/phpunit_test_', $temp->getValue());
        self::assertDirectoryExists($temp->getValue());

        $temp->clear();
    }

    public function testClear(): void
    {
        $temp = InMemoryFileSystemPath::create();

        $file1 = tempnam($temp->getValue(), 'test1');
        $file2 = tempnam($temp->getValue(), 'test2');
        $file3 = tempnam($temp->getValue(), 'test2');

        self::assertFileExists($file1);
        self::assertFileExists($file2);
        self::assertFileExists($file3);

        $temp->clear();
        self::assertDirectoryDoesNotExist($temp->getValue());
    }

    public function testNestedClear(): void
    {
        $temp = InMemoryFileSystemPath::create();

        $subDir = $temp->getValue(). DIRECTORY_SEPARATOR .'nested';
        mkdir($subDir);

        $file1 = tempnam($subDir, 'test1');
        $file2 = tempnam($temp->getValue(), 'test2');

        self::assertFileExists($file1);
        self::assertFileExists($file2);
        self::assertDirectoryExists($subDir);

        $temp->clear();
        self::assertDirectoryDoesNotExist($temp->getValue());
    }
}