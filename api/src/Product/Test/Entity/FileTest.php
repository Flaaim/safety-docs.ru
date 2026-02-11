<?php

namespace App\Product\Test\Entity;

use App\Product\Entity\File;
use App\Product\Test\TempDir;
use App\Shared\Domain\Service\Template\RootPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(File::class)]
class FileTest extends TestCase
{
    private TempDir $tempDir;
    public function setUp(): void
    {
        $this->tempDir = TempDir::create();
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
        $file = new File('/ppe/template.rar');
        $root = new RootPath($this->tempDir->getValue());
        $file->mergeRoot($root);

        self::assertEquals('/tmp/phpunit_test_/ppe/template.rar', $file->getFullPath());
    }

    #[DataProvider('fullPathProvider')]
    public function testTrimFullPath($value, $root, $expected): void
    {
        $file = new File($value);
        $rootPath = new RootPath($root);

        $file->mergeRoot($rootPath);

        self::assertEquals($expected, $file->getFullPath());

    }
    public function testFile()
    {
        $file = new File($this->tempFile());
        $root = new RootPath($this->tempDir->getValue());

        $file->mergeRoot($root);

        self::assertFileExists($file->getFullPath());
    }
    public static function fullPathProvider(): array
    {
        return [
            ['/ppe/template.rar', '/tmp', '/tmp/ppe/template.rar'],
            ['/ppe/template.rar/', '/tmp/', '/tmp/ppe/template.rar'],
            ['ppe/template.rar//', 'tmp/', '/tmp/ppe/template.rar'],
            ['//ppe/template.rar//', '//tmp/', '/tmp/ppe/template.rar'],
        ];
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