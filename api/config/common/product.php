<?php

declare(strict_types=1);

use App\Flusher;
use App\Product\Command\Images\GetAll\Handler as GetAllImagesHandler;
use App\Product\Command\Images\Add\Handler as AddImagesHandler;
use App\Product\Entity\ProductRepository;
use App\Product\Query\ProductQuery;
use App\Product\Service\File\DirectoryCreator;
use App\Product\Service\File\DirectoryCreatorInterface;
use App\Product\Service\File\FileNameGeneratorInterface;
use App\Product\Service\File\FileRemover;
use App\Product\Service\File\FileRemoverInterface;
use App\Product\Service\File\FileUploader;
use App\Product\Service\File\FileUploaderInterface;
use App\Product\Service\File\RandomFileNameGenerator;
use App\Shared\Domain\Query\ProductQueryInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use App\Shared\Domain\ValueObject\FileSystem\ImageSystemPath;
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
    DirectoryCreatorInterface::class => DI\get(DirectoryCreator::class),
    FileNameGeneratorInterface::class => DI\get(RandomFileNameGenerator::class),

    GetAllImagesHandler::class => function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);

        $productRepository = new ProductRepository($em);
        $imagePath = $container->get(ImageSystemPath::class);

        return new GetAllImagesHandler(
            $productRepository,
            $imagePath
        );
    },
    AddImagesHandler::class => function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);

        $productRepository = new ProductRepository($em);
        $imagePath = $container->get(ImageSystemPath::class);
        $flusher = new Flusher($em);

        return new AddImagesHandler(
            $productRepository,
            $flusher,
            new FileUploader($imagePath, new DirectoryCreator()),
            new RandomFileNameGenerator()
        );
    }

];