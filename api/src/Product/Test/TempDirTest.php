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
}