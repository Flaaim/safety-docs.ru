<?php

declare(strict_types=1);

use App\Shared\Domain\Service\File\DirectoryCreator;
use App\Shared\Domain\Service\File\DirectoryCreatorInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use App\Template\Query\Category\CategoryFetcher;
use App\Template\Query\Direction\DirectionFetcher;
use App\Template\ReadModel\CategoryFetcherInterface;
use App\Template\ReadModel\DirectionFetcherInterface;
use App\Template\Service\File\FileNameGeneratorInterface;
use App\Template\Service\File\FileUploader;
use App\Template\Service\File\FileUploaderInterface;
use App\Template\Service\File\RandomFilenameGenerator;
use Psr\Container\ContainerInterface;

return [
    DirectionFetcherInterface::class => DI\autowire(DirectionFetcher::class),
    CategoryFetcherInterface::class => DI\autowire(CategoryFetcher::class),
    FileUploaderInterface::class => function (ContainerInterface $container) {
        return new FileUploader(
            $container->get(FileSystemPathInterface::class),
            $container->get(DirectoryCreatorInterface::class),
            $container->get(FileNameGeneratorInterface::class),
        );
    },
    FileSystemPathInterface::class => DI\get(FileSystemPath::class),
    DirectoryCreatorInterface::class => DI\get(DirectoryCreator::class),
    FileNameGeneratorInterface::class => DI\get(RandomFilenameGenerator::class),
];