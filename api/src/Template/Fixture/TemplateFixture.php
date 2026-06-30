<?php

namespace App\Template\Fixture;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class TemplateFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $text  = <<<EOT
        Вопросы безопасности на рабочем месте требуют скрупулезного подхода к бумажной работе. Чтобы вам было проще ориентироваться в мире нормативных актов и приказов по охране труда, я собрал полный список документации по охране труда, распределив её по основным направлениям работы организации.
        EOT;

        $safetyDirection = new Direction(
            DirectionId::generate(),
            'Охрана труда',
            'Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.',
            $text,
            Slug::generate('Охрана труда')->getValue(),
        );

        $fireDirection = new Direction(
            DirectionId::generate(),
            'Пожарная безопасность',
            'Собраны коллекции документов по направлению работы пожарной безопасности',
            'Все что относиться к организации обеспечения пожарной безопасности на предприятии, инструктажи, обучение, первичные средства пожаротушения, тренировки эвакуации и т.д.',
            Slug::generate('Пожарная безопасность')
        );

        $category = new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Описание службы охраны труда',
            'Текст службы охраны труда',
            Slug::generate('Служба охраны труда')->getValue(),
            $safetyDirection
        );

        $manager->persist($category);
        $manager->persist($safetyDirection);
        $manager->persist($fireDirection);

        $manager->flush();
    }
}
