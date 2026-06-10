<?php

declare(strict_types=1);

use App\Distribution\Service\ContactFileImporter;
use App\Distribution\Service\ContactFileImporterInterface;
use App\Distribution\Service\ContactImportFileRemover;
use App\Distribution\Service\ContactImportFileRemoverInterface;
use App\Distribution\Service\ContactImportFileUploader;
use App\Distribution\Service\ContactImportFileUploaderInterface;
use App\Shared\Domain\Service\File\DirectoryCreatorInterface;
use App\Shared\Domain\ValueObject\FileSystem\DistributionSystemPath;
use Psr\Container\ContainerInterface;

return [
    ContactImportFileUploaderInterface::class => function (ContainerInterface $container) {
        return new ContactImportFileUploader(
            $container->get(DistributionSystemPath::class),
            $container->get(DirectoryCreatorInterface::class),
        );
    },
    ContactImportFileRemoverInterface::class => function (ContainerInterface $container) {
        return new ContactImportFileRemover(
            $container->get(DistributionSystemPath::class)
        );
    },
    ContactFileImporterInterface::class => function (ContainerInterface $container) {
        return new ContactFileImporter(
            $container->get(DistributionSystemPath::class),
        );
    }
];
