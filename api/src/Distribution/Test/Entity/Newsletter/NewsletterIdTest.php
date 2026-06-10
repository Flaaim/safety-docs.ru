<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Newsletter;

use App\Distribution\Entity\Newsletter\NewsletterId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class NewsletterIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new NewsletterId($value = Uuid::uuid4()->toString());

        self::assertSame($value, $id->getValue());
    }

    public function testCase(): void
    {
        $value = mb_strtoupper(Uuid::uuid4()->toString());
        $id = new NewsletterId($value);
        self::assertSame(mb_strtolower($value), $id->getValue());
    }
    public function testInvalid(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new NewsletterId('invalid');
    }

    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new NewsletterId('');
    }

    public function testEquals(): void
    {
        $id1 = new NewsletterId(Uuid::uuid4()->toString());
        $id2 = new NewsletterId(Uuid::uuid4()->toString());
        self::assertFalse($id1->equals($id2));
        self::assertTrue($id1->equals($id1));
    }
}
