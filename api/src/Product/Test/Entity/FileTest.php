<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\File;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\InMemoryFileSystemPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(File::class)]
class FileTest extends TestCase
{
    private InMemoryFileSystemPath $tempDir;
    public function setUp(): void
    {
        $this->tempDir = InMemoryFileSystemPath::create();
    }

    public function testSuccess(): void
    {
        $file = new File('/ppe/template.rar');
        self::assertEquals('ppe/template.rar', $file->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new File('');
    }
    #[DataProvider('fileValueProvider')]
    public function testTrimValue($actual, $expected): void
    {
        $file = new File($actual);
        self::assertEquals($expected, trim($file->getValue()));
    }
    public function testMergeRoot(): void
    {
        $file = new File($filename = $this->tempFile());
        $root = new FileSystemPath($this->tempDir->getValue());
        $file->mergeRoot($root);

        self::assertEquals('/tmp/phpunit_test_/'.$filename, $file->getFile());
    }


    public function testMergeAlready(): void
    {
        $file = new File($this->tempFile());
        $root = new FileSystemPath($this->tempDir->getValue());

        $file->mergeRoot($root);

        self::expectException(\DomainException::class);
        self::expectExceptionMessage('Root path already merged.');
        $file->mergeRoot($root);

    }
    public function testFile()
    {
        $file = new File($this->tempFile());
        $root = new FileSystemPath($this->tempDir->getValue());

        $file->mergeRoot($root);

        self::assertFileExists($file->getFile());
    }
    public function testExists(): void
    {
        $file = new File($this->tempFile());
        $root = new FileSystemPath($this->tempDir->getValue());

        $file->mergeRoot($root);
        self::assertTrue($file->exists());
    }

    public function testNotExists(): void
    {
        $file = new File($this->tempFile());
        self::assertFalse($file->exists());
    }

    public static function fileValueProvider(): array
    {
        return [
            ['ppe/template.rar', 'ppe/template.rar'],
            ['/ppe/template.rar', 'ppe/template.rar'],
            ['/ppe/template.rar/', 'ppe/template.rar'],
            ['/ppe/template.rar///', 'ppe/template.rar'],
            ['/ ppe/template.rar///', 'ppe/template.rar'],
            ['/ ppe/template.rar /', 'ppe/template.rar'],
        ];
    }

    private function tempFile(): string
    {
        $file1 = tempnam($this->tempDir->getValue(), 'file1');
        return basename($file1);
    }

    public function tearDown(): void
    {
        $this->tempDir->clear();
    }
}