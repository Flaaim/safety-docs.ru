<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\Launch;

use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Test\Entity\NewsletterBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const NEWSLETTER_ID = '2b0bae2f-565d-4cc9-b7af-1ee2deb9aaa6';
    public const NEWSLETTER_NOT_FOUND_ID = 'a766fc77-53b0-4701-843c-e1149412af84';
    public function load(ObjectManager $manager): void
    {
        $project = new Project(
            ProjectId::generate(),
            'Блог охраны труда'
        );

        $manager->persist($project);

        $newsletter = (new NewsletterBuilder())
            ->withId(new NewsletterId(self::NEWSLETTER_ID))
            ->withProjectId($project->getId())
            ->build();

        $manager->persist($newsletter);

        $manager->flush();
    }
}