<?php

namespace App\Product\Entity;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UnassignedProductsFetcher
{
    private Connection $conn;
    public function __construct(EntityManagerInterface $em)
    {
        $this->conn = $em->getConnection();
    }

    /**
     * @return array<int, array{id:string, name:string}>
     */
    public function fetchAllFree(): array
    {
        $sql = '
            SELECT p.id, p.name 
            FROM products p
            LEFT JOIN categories c ON p.id = c.product_id
            WHERE c.product_id IS NULL
            ORDER BY p.name ASC
        ';

        return $this->conn->fetchAllAssociative($sql);
    }
}