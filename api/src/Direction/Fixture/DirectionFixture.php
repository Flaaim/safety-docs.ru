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
        Все образцы документов, представленные на сайте, актуальны на 2026 год и могут быть адаптированы под специфику вашего производства.
        EOT;

        $direction = new Direction(
            DirectionId::generate(),
            'Охрана труда — документация по направлениям',
            'Собраны комплекты образцов документов по основным направлениями работы по охране труда и техники безопасности.',
            $text,
            new Slug('safety')
        );

        $category = new Category(
            CategoryId::generate(),
            'Служба охраны труда',
            'Описание службы охраны труда',
            'Текст службы охраны труда',
            new Slug('service'),
            $direction
        );

        $manager->persist($category);
        $manager->persist($direction);

        $manager->flush();
    }
}