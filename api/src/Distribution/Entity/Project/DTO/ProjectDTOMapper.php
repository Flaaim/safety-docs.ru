<?php

declare(strict_types=1);

namespace App\Distribution\Entity\Project\DTO;

use App\Distribution\Entity\Project\Project;

final class ProjectDTOMapper
{
    public function map(?Project $project): ?ProjectDTO
    {
        if (null === $project) {
            return null;
        }
        return ProjectDTO::fromProject($project);
    }

    public function mapCollection(array $projects): array
    {
        $result = [];
        foreach ($projects as $project) {
            $result[] = $this->map($project);
        }
        return $result;
    }
}
