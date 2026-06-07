<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

final class File
{
    public function __construct(
        private string $id,
        private string $name,
        private \DateTimeImmutable $date,
        private bool $complete = false
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
    public function isComplete(): bool
    {
        return $this->complete;
    }
    public function complete(): void
    {
        $this->complete = true;
    }
}