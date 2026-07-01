<?php

namespace Test\Functional\Template\GetAll;

use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class RequestFixture extends AbstractFixture
{
    public const DIRECTION_ID = '37e9c865-8401-4339-bb23-73a25b85e7b3';
    public const DIRECTION_TITLE = 'Охрана труда';
    public const DIRECTION_DESCRIPTION = 'Собраны комплекты документов';
    public const DIRECTION_TEXT = 'some_text';
    public function load(ObjectManager $manager): void
    {
        $slug = Slug::generate(self::DIRECTION_TITLE)->getValue();

        $direction = new Direction(
            new DirectionId(self::DIRECTION_ID),
            self::DIRECTION_TITLE,
            self::DIRECTION_DESCRIPTION,
            self::DIRECTION_TEXT,
            $slug,
        );
        $manager->persist($direction);

        $manager->flush();
    }
}