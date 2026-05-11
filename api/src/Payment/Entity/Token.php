<?php

namespace App\Payment\Entity;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Token
{
    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private string $value;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private DateTimeImmutable $expired;
    public function __construct(string $value, DateTimeImmutable $expired)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expired = $expired;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    public function getExpired(): DateTimeImmutable
    {
        return $this->expired;
    }

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new \DomainException('Token is invalid.');
        }
        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Token is expired.');
        }
    }
    public function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }
    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expired <= $date;
    }
}
