<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllProjects;

final class ProjectCollections implements \JsonSerializable
{
    public function __construct(
        public readonly array $projects,
    ) {
    }
    public function jsonSerialize(): array
    {
        return [
            'projects' => array_map(fn($project) => [
                'id' => $project['id'],
                'name' => $project['name'],
                'contact_count' => $project['contact_count'],
            ], $this->projects),
        ];
    }
}
