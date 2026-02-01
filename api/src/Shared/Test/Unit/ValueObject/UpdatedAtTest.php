<?php

namespace App\Shared\Test\Unit\ValueObject;

use App\Shared\Domain\ValueObject\UpdatedAt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UpdatedAtTest extends TestCase
{
    #[dataProvider('validDotDataProvider')]
    public function testDot($actual, $expected): void
    {
        $updatedAt = UpdatedAt::createDot($actual);
        self::assertEquals($expected, $updatedAt->format('d.m.Y'));
    }
    public static function validDotDataProvider(): array
    {
        return [
            ['01.01.2023', '01.01.2023'],
            ['31.12.2023', '31.12.2023'],
            ['29.02.2024', '29.02.2024'],
        ];
    }
    #[dataProvider('validSlashDataProvider')]
    public function testSlash($actual, $expected): void
    {
        $updatedAt = UpdatedAt::createSlash($actual);
        self::assertEquals($expected, $updatedAt->format('d/m/Y'));
    }
    public static function validSlashDataProvider(): array
    {
        return [
            ['01/01/2023', '01/01/2023'],
            ['31/12/2023', '31/12/2023'],
            ['29/02/2024', '29/02/2024'],
        ];
    }
    #[dataProvider('validDashDataProvider')]
    public function testDash($actual, $expected): void
    {
        $updatedAt = UpdatedAt::createDash($actual);
        self::assertEquals($expected, $updatedAt->format('d-m-Y'));
    }
    public static function validDashDataProvider(): array
    {
        return [
            ['01-01-2023', '01-01-2023'],
            ['31-12-2023', '31-12-2023'],
            ['29-02-2024', '29-02-2024'],
        ];
    }
    #[dataProvider('validIsoDataProvider')]
    public function testIso($actual, $expected): void
    {
        $updatedAt = UpdatedAt::createIso($actual);
        self::assertEquals($expected, $updatedAt->format('Y-m-d'));
    }
    public static function validIsoDataProvider(): array
    {
        return [
            ['2023-01-01', '2023-01-01'],
            ['2023-12-31', '2023-12-31'],
            ['2024-02-29', '2024-02-29'],
        ];
    }
    #[dataProvider('invalidDataProvider')]
    public function testInvalid($value): void
    {
        self::expectException(\DomainException::class);
        UpdatedAt::createDot($value);
    }
    public static function invalidDataProvider(): array
    {
        return [
          ['foo'],
          ['29.02.2023'],
          ['41.12.2024'],
        ];
    }
    public function testTrim(): void
    {
        $updatedAt = UpdatedAt::createDot(' 12.11.2025 ');
        self::assertEquals('12.11.2025', $updatedAt->format('d.m.Y'));
    }
    public function testFormat(): void
    {
        $updatedAt = UpdatedAt::createDot('12.11.2025');
        self::assertEquals('12.11.2025', $updatedAt->format('d.m.Y'));
    }
}