<?php

declare(strict_types=1);

namespace App\Documents\Test\Unit\Entity;

use App\Documents\Entity\DocumentId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class DocumentIdTest extends TestCase
{
    public function testSuccess(): void
    {
        $id = new DocumentId($value = Uuid::uuid4()->toString());

        $this->assertNotNull($id->getValue());
        $this->assertSame($value, $id->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DocumentId('invalid');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new DocumentId('');
    }
    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new DocumentId(mb_strtoupper($value));

        $this->assertSame($value, $id->getValue());
    }
}