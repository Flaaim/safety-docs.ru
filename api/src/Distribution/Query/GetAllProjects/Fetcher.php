<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetAllProjects;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function fetch(): ProjectCollections
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('p.*, COUNT(pc.id) as contact_count')
            ->from('distribution_projects', 'p')
            ->leftJoin('p', 'project_contacts', 'pc', 'p.id = pc.project_id')
            ->groupBy('p.id')
            ->orderBy('p.name', 'ASC');

        $data = $qb->fetchAllAssociative();

        return new ProjectCollections($data);
    }
}
