<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use Webmozart\Assert\Assert;

final class Status
{
    const STATUSES = [
        'created',
        'completed',
        'failed',
        'process',
    ];
    private string $status;
    public function __construct(string $status)
    {
        Assert::oneOf($status, self::STATUSES);
        $this->status = $status;
    }
    public function getValue(): string
    {
        return $this->status;
    }
    public static function created(): self
    {
        return new self('created');
    }

    public static function completed(): self
    {
        return new self('completed');
    }
    public static function process(): self
    {
        return new self('process');
    }
    public static function failed(): self
    {
        return new self('failed');
    }
}