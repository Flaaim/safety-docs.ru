<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;

return [
  CallableResolverInterface::class => function (ContainerInterface $container) {
    return new CallableResolver($container);
  }
];
