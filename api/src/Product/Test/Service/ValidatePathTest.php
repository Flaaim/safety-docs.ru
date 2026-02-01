<?php

namespace App\Product\Test\Service;

use App\Product\Service\ValidatePath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatePathTest extends TestCase
{
    public static function allowedProvider(): array
    {
        return [
            ['fire/ot1794'],
            ['test/ot17941'],
        ];
    }
    #[DataProvider('allowedProvider')]
    public function testSuccess(string $path): void
    {
        $validatePath = new ValidatePath();
        self::assertTrue((bool)$validatePath->validate($path));
    }

    public static function notAllowedProvider(): array
    {
        return [
            ['195/1794/1974.1.docx'],
            ['test/abc/'],
            ['fire/1794abs'],
            ['fire/1971/'],
            ['fire/ot201/']
        ];
    }
    #[DataProvider('notAllowedProvider')]
    public function testInvalid(string $path): void
    {
        $validatePath = new ValidatePath();
        self::assertFalse((bool)$validatePath->validate($path));
    }
}