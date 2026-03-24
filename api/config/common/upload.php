<?php

declare(strict_types=1);

use App\Product\Command\Add\Upload\Handler as UploadHandler;
use App\Product\Service\UploadFileHandler;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPath;
use Psr\Container\ContainerInterface;

return [
    UploadHandler::class => function (ContainerInterface $container) {
        return new UploadHandler(
            $container->get(FileSystemPath::class),
            new UploadFileHandler()
        );
    }
];