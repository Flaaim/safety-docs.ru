<?php

namespace App\Shared\Domain\ValueObject;

use DateTimeImmutable;
use DomainException;

class UpdatedAt
{
    public const FORMAT_DOT = 'd.m.Y';
    public const FORMAT_DASH = 'd-m-Y';
    public const FORMAT_SLASH = 'd/m/Y';
    public const FORMAT_ISO = 'Y-m-d';
    private DateTimeImmutable $value;
    private function __construct(string $value, string $format)
    {
        $value = DateTimeImmutable::createFromFormat($format, trim($value));
        if ($value === false) {
            throw new DomainException(sprintf('Invalid date format. Expected: %s', $format));
        }
        $errors = DateTimeImmutable::getLastErrors();
        if ($errors && $errors['warning_count'] > 0) {
            throw new DomainException(sprintf('Invalid date format. Expected: %s', $format));
        }
        $this->value = $value;
    }
    public static function create($value, string $format = self::FORMAT_DOT): self
    {
        return new self($value, $format);
    }
    public static function createDot($value): self
    {
        return new self($value, self::FORMAT_DOT);
    }
    public static function createDash($value): self
    {
        return new self($value, self::FORMAT_DASH);
    }
    public static function createSlash($value): self
    {
        return new self($value, self::FORMAT_SLASH);
    }
    public static function createIso($value): self
    {
        return new self($value, self::FORMAT_ISO);
    }
    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }
    public function format(string $format): string
    {
        return $this->value->format($format);
    }
    public function toString(): string
    {
        return $this->format('Y-m-d H:i:s');
    }
}
