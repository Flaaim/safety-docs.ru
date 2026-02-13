<?php

namespace App\Sender\Entity;

use Webmozart\Assert\Assert;

class MessageStatus
{
    const RECEIVED = 'received';
    const FAILED = 'failed';

    public function __construct(
        private readonly string $value
    ){
        Assert::oneOf($value, [self::RECEIVED, self::FAILED]);
    }

    public static function received(): self
    {
        return new self('received');
    }
    public static function failed(): self
    {
        return new self('failed');
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