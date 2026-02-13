<?php

namespace App\Sender\Entity;

use Webmozart\Assert\Assert;

class MessageStatus
{
    const RECEIVED = 'received';
    const FAILED = 'failed';
    const PENDING = 'pending';

    public function __construct(
        private readonly string $value
    ){
        Assert::oneOf($value, [self::RECEIVED, self::FAILED, self::PENDING]);
    }

    public static function received(): self
    {
        return new self('received');
    }
    public static function failed(): self
    {
        return new self('failed');
    }
    public static function pending(): self
    {
        return new self('pending');
    }
    public function getValue(): string
    {
        return $this->value;
    }
    public function isReceived(): bool
    {
        return $this->value === self::RECEIVED;
    }
    public function isFailed(): bool
    {
        return $this->value === self::FAILED;
    }
}