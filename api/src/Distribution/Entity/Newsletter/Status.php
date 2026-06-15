<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Newsletter;

use Webmozart\Assert\Assert;

final class Status
{
    public const STATUSES = [
        'created',
        'completed',
        'failed',
        'processed',
        'archived',
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
        return new self(NewsletterStatus::Created->value);
    }

    public static function completed(): self
    {
        return new self(NewsletterStatus::Completed->value);
    }
    public static function processed(): self
    {
        return new self(NewsletterStatus::Processed->value);
    }
    public static function failed(): self
    {
        return new self(NewsletterStatus::Failed->value);
    }
    public static function archived(): self
    {
        return new self(NewsletterStatus::Archived->value);
    }
}
