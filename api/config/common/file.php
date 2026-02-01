<?php

declare(strict_types=1);

use App\Product\Entity\UploadDir;
use App\Product\Service\FileHandler;
use App\Product\Service\ValidatePath;
use App\Shared\Domain\Service\Template\TemplatePath;
use Psr\Container\ContainerInterface;

return [
    FileHandler::class => function (ContainerInterface $container) {
        return new FileHandler($container->get(UploadDir::class));
    },
    UploadDir::class => function(ContainerInterface $container){
        return new UploadDir(
            $container->get(TemplatePath::class),
            new ValidatePath()
        );
    }
];