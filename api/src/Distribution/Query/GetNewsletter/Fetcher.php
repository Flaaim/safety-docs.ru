<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetNewsletter;

use Doctrine\DBAL\Connection;

final class Fetcher
{
    public function __construct(
        private readonly Connection $connection,
    ) {}
    public function fetch(Query $query): Newsletter
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('n.*')
            ->from('newsletters', 'n')
            ->where($qb->expr()->eq('n.id', ':id'))
            ->setParameter('id', $query->newsletterId);

        $data = $qb->executeQuery()->fetchAssociative();

        return new Newsletter(
            $data['newsletter_id'],
            $data['templateId'],
            $data['subject'],
            $data['status'],
            $data['project_id'],
            $data['created_at']
        );
    }
}