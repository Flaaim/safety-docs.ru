<?php

namespace App\Product\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TempDir::class)]
class TempDirTest extends TestCase
{
    public function testSuccess(): void
    {
        $temp = TempDir::create();

        self::assertEquals('/tmp/phpunit_test_', $temp->getValue());
        self::assertDirectoryExists($temp->getValue());
    }

    public function testClear(): void
    {
        $temp = TempDir::create();

        $file1 = tempnam($temp->getValue(), 'test1');
        $file2 = tempnam($temp->getValue(), 'test2');
        $file3 = tempnam($temp->getValue(), 'test2');

        self::assertFileExists($file1);
        self::assertFileExists($file2);
        self::assertFileExists($file3);

        $temp->clear();

        self::assertCount(2, scandir($temp->getValue()));
    }
}