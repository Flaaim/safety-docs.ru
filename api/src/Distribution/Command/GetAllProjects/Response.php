<?php

declare(strict_types=1);

namespace App\Distribution\Command\GetAllProjects;

use App\Distribution\Entity\File\DTO\FileDTO;
use App\Distribution\Entity\Project\DTO\ProjectDTO;

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
            'projects' => array_map(fn(ProjectDTO $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'contacts' => $project->contacts,
            ], $this->projects),
        ];
    }
}
