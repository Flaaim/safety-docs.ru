<?php

declare(strict_types=1);

namespace App\Distribution\Command\GetAllProjects;

use App\Distribution\Entity\Project\DTO\ProjectDTOMapper;
use App\Distribution\Entity\Project\ProjectRepository;

final class Handler
{
    public function __construct(
        private readonly ProjectRepository $projects,
        private readonly ProjectDTOMapper $projectDTOMapper,
    ) {
    }
    public function handle(): Response
    {
        $projects = $this->projects->findAll();

        $result = $this->projectDTOMapper->mapCollection($projects);

        return Response::fromResult($result);
    }
}
