<?php

declare(strict_types=1);

namespace Test\Functional\Distribution\Newsletter\GetAllPaginated;

use App\Distribution\Entity\Newsletter\Newsletter;
use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\Status;
use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RequestFixture extends AbstractFixture
{
    public const NEWSLETTER_ID = '20158792-4afa-443f-b1ab-552272148947';
    public const TEMPLATE_ID = 'd4d10922-471d-482a-873e-86f0d9d3d144';
    public function load(ObjectManager $manager): void
    {
        $project = new Project(
            ProjectId::generate(),
            'Блог охраны труда'
        );
        $newsletter = new Newsletter(
            new NewsletterId(self::NEWSLETTER_ID),
            'Обновления сайта',
            self::TEMPLATE_ID,
            Status::created(),
            $project->getId(),
            new \DateTimeImmutable(),
        );
        $manager->persist($project);

        $manager->persist($newsletter);
        $manager->flush();
    }
}