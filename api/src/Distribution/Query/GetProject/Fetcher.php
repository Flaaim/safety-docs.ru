<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetProject;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function fetch(Query $query): Project
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('dp.id AS project_id', 'dp.name AS project_name', 'pc.email AS contact_email')
            ->from('distribution_projects', 'dp')
            ->leftJoin('dp', 'project_contacts', 'pc', 'dp.id = pc.project_id')
            ->where($qb->expr()->eq('dp.id', ':id'))
            ->setParameter('id', $query->projectId);

        $data = $qb->executeQuery()->fetchAssociative();
        $project = new Project(
            $data['id'],
            $data['name'],
            $data['contacts'],
        );


    }
}