<?php

namespace App\Template\Test\Unit\Entity\Document;

use App\Template\Entity\Document\Filename;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Filename::class)]
class FilenameTest extends TestCase
{
    public function testSuccess(): void
    {

        $filename = new Filename($value = '6dbcb00a-e2bd-4a39-b1ea-aa09568ab1cc.docx');
        self::assertEquals($value, $filename->getValue());
    }
    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Filename('');
    }

    public function testCase(): void
    {
        $value = mb_strtoupper('6dbcb00a-e2bd-4a39-b1ea-aa09568ab1cc.docx');
        $filename = new Filename($value);

        self::assertEquals('6dbcb00a-e2bd-4a39-b1ea-aa09568ab1cc.docx', $filename->getValue());
    }

    #[DataProvider('valuesProvider')]
    public function testInvalid($value): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Filename($value);
    }

    public static function valuesProvider(): array
    {
        return [
            ['template100!.@1.1.rar'],
            ['template100rar'],
        ];
    }
}
