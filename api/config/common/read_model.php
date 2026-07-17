<?php

declare(strict_types=1);

use App\ReadModel\Template\GetTemplatesHandler;
use Doctrine\DBAL\Connection;

return [
    GetTemplatesHandler::class => DI\autowire(GetTemplatesHandler::class)
        ->constructorParameter('connection', DI\get(Connection::class)),
];
