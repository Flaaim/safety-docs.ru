<?php

namespace App\Direction\Fixture;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Direction\DirectionId;
use App\Direction\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class DirectionFixture extends AbstractFixture
{

    public function load(ObjectManager $manager): void
    {
        $text  = <<<EOT
        Вопросы безопасности на рабочем месте требуют скрупулезного подхода к бумажной работе. Чтобы вам было проще ориентироваться в мире нормативных актов и приказов по охране труда, я собрал полный список документации по охране труда, распределив её по основным направлениям работы организации.
        EOT;

        $safetyDirection = new Direction(
            DirectionId::generate(),
            'Охрана труда — документация по направлениям',
            'Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.',
            $text,
            new Slug('safety')
        );

        $fireDirection = new Direction(
            DirectionId::generate(),
            'Пожарная безопасность — документация по направлениям',
            'Собраны коллекции документов по направлению работы пожарной безопасности',
            'Все что относиться к организации обеспечения пожарной безопасности на предприятии, инструктажи, обучение, первичные средства пожаротушения, тренировки эвакуации и т.д.',
            new Slug('fire-safety')
        );

        $category = new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Описание службы охраны труда',
            'Текст службы охраны труда',
            new Slug('service'),
            $safetyDirection
        );

        $manager->persist($category);
        $manager->persist($safetyDirection);
        $manager->persist($fireDirection);

        $manager->flush();
    }
}