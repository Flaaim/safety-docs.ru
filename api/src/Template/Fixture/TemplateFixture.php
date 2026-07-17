<?php

declare(strict_types=1);

namespace App\Template\Fixture;

use App\Shared\Domain\ValueObject\Currency;
use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Direction\DirectionId;
use App\Template\Entity\Document\Amount;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Document\DocumentId;
use App\Template\Entity\Document\Filename;
use App\Template\Entity\Slug;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

/**
 * Seed data for Direction → Category → Template (Document) hierarchy.
 */
final class TemplateFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $occupationalSafetyDirection = $this->createDirection(
            'Охрана труда',
            'Собраны комплекты образцов документов по охране труда и технике безопасности.',
            <<<'EOT'
Вопросы безопасности на рабочем месте требуют скрупулёзного подхода к бумажной работе.
Комплекты документов по охране труда помогут организовать службу ОТ, СУОТ, инструктажи и приказы.
EOT
        );

        $industrialSafetyDirection = $this->createDirection(
            'Промышленная безопасность',
            'Документы для организации промышленной безопасности на опасных производственных объектах.',
            <<<'EOT'
Промышленная безопасность охватывает требования к эксплуатации ОПО, ведению журналов,
инструктажам и локальным нормативным актам по промышленной безопасности.
EOT
        );

        $manager->persist($occupationalSafetyDirection);
        $manager->persist($industrialSafetyDirection);

        $occupationalSafetyCategories = [
            $this->createCategory(
                $occupationalSafetyDirection,
                'Инструкции по охране труда',
                'Типовые инструкции по охране труда для работников и специалистов.',
                'Готовые инструкции по ОТ для адаптации под условия вашей организации.'
            ),
            $this->createCategory(
                $occupationalSafetyDirection,
                'Положения',
                'Положения по охране труда и системе управления охраной труда.',
                'Локальные положения, регламентирующие организацию работ по охране труда.'
            ),
            $this->createCategory(
                $occupationalSafetyDirection,
                'Приказы',
                'Приказы по охране труда для назначения ответственных и организации работ.',
                'Шаблоны приказов по ОТ: назначение ответственных, комиссии, допуски.'
            ),
        ];

        $industrialSafetyCategories = [
            $this->createCategory(
                $industrialSafetyDirection,
                'Инструкции по ПБ',
                'Инструкции по промышленной безопасности для работников ОПО.',
                'Инструкции по ПБ для персонала, обслуживающего опасные производственные объекты.'
            ),
            $this->createCategory(
                $industrialSafetyDirection,
                'Журналы',
                'Журналы учёта по промышленной безопасности.',
                'Формы журналов для регистрации инструктажей, проверок и работ повышенной опасности.'
            ),
        ];

        foreach ([...$occupationalSafetyCategories, ...$industrialSafetyCategories] as $category) {
            $manager->persist($category);
        }

        $templatesByCategory = [
            $occupationalSafetyCategories[0]->getSlug() => [
                ['Инструкция для водителя', 190.00],
                ['Инструкция по охране труда при работе на высоте', 220.00],
                ['Инструкция по охране труда для офисных работников', 150.00],
            ],
            $occupationalSafetyCategories[1]->getSlug() => [
                ['Положение о СУОТ', 350.00],
                ['Положение о службе охраны труда', 280.00],
                ['Положение о порядке проведения инструктажей', 200.00],
            ],
            $occupationalSafetyCategories[2]->getSlug() => [
                ['Приказ о назначении ответственного за охрану труда', 120.00],
                ['Приказ о создании комиссии по охране труда', 130.00],
                ['Приказ о проведении обучения по охране труда', 140.00],
            ],
            $industrialSafetyCategories[0]->getSlug() => [
                ['Инструкция по промышленной безопасности для слесаря', 210.00],
                ['Инструкция по ПБ при работах на ОПО', 240.00],
                ['Инструкция по действиям при аварии на ОПО', 180.00],
            ],
            $industrialSafetyCategories[1]->getSlug() => [
                ['Журнал регистрации инструктажей по ПБ', 160.00],
                ['Журнал учёта работ повышенной опасности', 170.00],
                ['Журнал проверки состояния промышленной безопасности', 155.00],
            ],
        ];

        $categories = [...$occupationalSafetyCategories, ...$industrialSafetyCategories];

        foreach ($categories as $category) {
            foreach ($templatesByCategory[$category->getSlug()] as [$name, $amount]) {
                $template = $this->createTemplate($category, $name, $amount);
                $manager->persist($template);
            }
        }

        $manager->flush();
    }

    private function createDirection(string $title, string $description, string $text): Direction
    {
        return new Direction(
            new DirectionId(Uuid::uuid4()->toString()),
            $title,
            $description,
            $text,
            Slug::generate($title)->getValue(),
        );
    }

    private function createCategory(
        Direction $direction,
        string $title,
        string $description,
        string $text
    ): Category {
        return new Category(
            new CategoryId(Uuid::uuid4()->toString()),
            $title,
            $description,
            $text,
            Slug::generate($title)->getValue(),
            $direction
        );
    }

    /**
     * Template in ubiquitous language maps to Document aggregate in persistence.
     */
    private function createTemplate(Category $category, string $name, float $amount): Document
    {
        return new Document(
            new DocumentId(Uuid::uuid4()->toString()),
            $name,
            new Amount($amount, new Currency('RUB')),
            new Filename(Uuid::uuid4()->toString() . '.docx'),
            Slug::generate($name)->getValue(),
            $category
        );
    }
}
