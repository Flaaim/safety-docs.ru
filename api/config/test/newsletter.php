<?php

declare(strict_types=1);

use App\Distribution\Service\FakeNewsletterLauncher;
use App\Distribution\Service\NewsletterLauncherInterface;
use Psr\Container\ContainerInterface;


return [
    NewsletterLauncherInterface::class => function (ContainerInterface $container): NewsletterLauncherInterface {
        return new FakeNewsletterLauncher();
    }
];
