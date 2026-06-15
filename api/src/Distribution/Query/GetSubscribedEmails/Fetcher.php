<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetSubscribedEmails;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }


    public function fetch(Query $query): \Generator
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pc.email')
            ->from('project_contacts', 'pc')
            ->where($qb->expr()->eq('pc.project_id', ':project_id'))
            ->andWhere($qb->expr()->eq('pc.is_unsubscribed', 0))
            ->setParameter('project_id', $query->projectId);

        $result = $qb->executeQuery();

        foreach ($result->iterateColumn() as $email) {
            yield $email;
        }
    }
}
