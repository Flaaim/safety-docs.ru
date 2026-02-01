<?php

namespace Test\Functional\Payment;

use App\Payment\Entity\Token;
use App\Shared\Domain\ValueObject\Id;

class TokenBuilder
{
    private string $value;
    private \DateTimeImmutable $expired;
    public function __construct()
    {
        $this->value = new Id('392b1c38-f3e4-4533-a6cb-5b4e7c08d91f');
        $this->expired = new \DateTimeImmutable('+ 1 hour');
    }

    public function withValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
    public function withExpired(\DateTimeImmutable $expired): self
    {
        $this->expired = $expired;
        return $this;
    }

    public function build(): Token
    {
        return new Token($this->value, $this->expired);
    }
}