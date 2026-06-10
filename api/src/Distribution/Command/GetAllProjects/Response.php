<?php

declare(strict_types=1);

namespace App\Distribution\Command\GetAllProjects;

final class Response implements \JsonSerializable
{
    private function __construct(
        private readonly array $projects,
    ) {
    }
    public static function fromResult(array $projects): self
    {
        return new self(
            $projects,
        );
    }
    public function jsonSerialize(): array
    {
        return [
            'projects' => $this->projects,
        ];
    }
}