<?php

declare(strict_types=1);

use App\Product\Entity\ProductRepository;
use App\Product\Query\ProductQuery;
use App\Product\Service\File\FileRemover;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Service\File\FileUploader;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\Query\ProductQueryInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    ProductQueryInterface::class => function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);
        $productRepository = new ProductRepository($em);

        return new ProductQuery($productRepository);
    },
    FileRemoverInterface::class => DI\get(FileRemover::class),
    FileUploaderInterface::class => DI\get(FileUploader::class),
    FileSystemPathInterface::class => DI\get(FileSystemPath::class),


];