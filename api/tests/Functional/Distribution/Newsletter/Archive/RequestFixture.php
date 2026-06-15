<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Archive;

use App\Distribution\Entity\Newsletter\Status;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Test\Entity\NewsletterBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const PROJECT_ID = '5d70292e-77c6-4403-afcf-21a2ae997341';
    public function load(ObjectManager $manager): void
    {
        $newsletter = (new NewsletterBuilder())
            ->withProjectId(new ProjectId(self::PROJECT_ID))
            ->withStatus(Status::completed())
            ->build();

        $manager->persist($newsletter);

        $manager->flush();
    }
}