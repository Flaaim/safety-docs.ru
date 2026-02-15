<?php

declare(strict_types=1);

use App\Product\Entity\UploadDir;
use App\Product\Service\FileHandler;
use App\Product\Service\ValidatePath;
use App\Product\Test\TempDir;
use App\Shared\Domain\Service\Template\RootPath;
use Psr\Container\ContainerInterface;

return [
    FileHandler::class => function (ContainerInterface $container) {
        return new FileHandler($container->get(UploadDir::class));
    },
    UploadDir::class => function(){
        return new UploadDir(
            new RootPath(TempDir::create()->getValue()),
            new ValidatePath()
        );
    }
];