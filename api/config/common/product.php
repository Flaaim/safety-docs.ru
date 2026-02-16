<?php

declare(strict_types=1);

use App\Product\Entity\ProductRepository;
use App\Product\Query\ProductQuery;
use App\Shared\Domain\Query\ProductQueryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    ProductQueryInterface::class => function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);
        $productRepository = new ProductRepository($em);

        return new ProductQuery($productRepository);
    }
];