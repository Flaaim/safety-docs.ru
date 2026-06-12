<?php

declare(strict_types=1);

namespace App\Distribution\Test\Entity\Newsletter;

use App\Distribution\Entity\Newsletter\Status;
use PHPUnit\Framework\TestCase;

final class StatusTest extends TestCase
{
    public function testCreate(): void
    {
        $status = Status::created();

        self::assertEquals('created', $status->getValue());
    }

    public function testInvalid(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Status('invalid');
    }

    public function testEmpty(): void
    {
        self::expectException(\InvalidArgumentException::class);
        new Status('');
    }
}
