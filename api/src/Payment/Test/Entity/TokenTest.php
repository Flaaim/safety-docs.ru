<?php

namespace App\Payment\Test\Entity;

use App\Payment\Entity\Token;
use App\Shared\Domain\ValueObject\Id;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Id::generate(),
            $expired = new DateTimeImmutable('+ 1 hour'),
        );

        $this->assertEquals($value, $token->getValue());
        $this->assertEquals($expired, $token->getExpired());
    }

    public function testCase(): void
    {
        $value = Id::generate();
        $token = new Token(mb_strtoupper($value), new DateTimeImmutable('+ 1 hour'));

        $this->assertEquals($value, $token->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Token('invalidString', new DateTimeImmutable('+ 1 hour'));
    }
    public function testExpired(): void
    {
        $token = new Token(
            Id::generate(),
            new DateTimeImmutable('+ 1 hour'),
        );
        $date = new DateTimeImmutable('now');

        $this->assertFalse($token->isExpiredTo($date));

        $date = new DateTimeImmutable('+ 2 hour');

        $this->assertTrue($token->isExpiredTo($date));
    }

    public function testEquals(): void
    {
        $token = new Token(
            Id::generate(),
            new DateTimeImmutable('+ 1 hour'),
        );

        $newToken = new Token(
            Id::generate(),
            new DateTimeImmutable('+ 1 hour'),
        );

        $this->assertFalse($token->isEqualTo($newToken->getValue()));
        $this->assertTrue($token->isEqualTo($token->getValue()));
    }

    public function testValidateInvalid(): void
    {
        $token = new Token(
            Id::generate(),
            new DateTimeImmutable('+ 1 hour'),
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Token is invalid.');

        $token->validate(Id::generate(), new DateTimeImmutable('now'));
    }

    public function testValidateExpired(): void
    {
        $token = new Token(
            $value = Id::generate(),
            new DateTimeImmutable('+ 1 hour'),
        );
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Token is expired.');

        $token->validate($value, new DateTimeImmutable('+ 2 hour'));
    }
}
