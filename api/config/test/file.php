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
    UploadDir::class => function(){
        return new UploadDir(
            new TemplatePath(sys_get_temp_dir()),
            new ValidatePath()
        );
    }
];