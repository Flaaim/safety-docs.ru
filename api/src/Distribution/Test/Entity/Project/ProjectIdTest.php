<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Project;

use App\Distribution\Entity\Project\ProjectId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class ProjectIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new ProjectId($value = Uuid::uuid4()->toString());

        self::assertSame($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = mb_strtoupper(Uuid::uuid4()->toString());
        $id = new ProjectId($value);
        self::assertSame(mb_strtolower($value), $id->getValue());
    }
    public function testInvalid(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new ProjectId('invalid');
    }

    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new ProjectId('');
    }

    public function testEquals(): void
    {
        $id1 = new ProjectId(Uuid::uuid4()->toString());
        $id2 = new ProjectId(Uuid::uuid4()->toString());
        self::assertFalse($id1->equals($id2));
        self::assertTrue($id1->equals($id1));
    }
}
