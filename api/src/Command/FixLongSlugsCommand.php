<?php

declare(strict_types=1);

namespace App\Command;

use App\Template\Entity\Document\Document;
use App\Template\Entity\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @psalm-suppress UnusedClass */
final class FixLongSlugsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('slug:fix')
            ->setDescription('Fix slug length');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $documents = $this->em->getRepository(Document::class);

        $documents = $documents->findAll();

        $updatedCount = 0;
        $batchSize = 100;

        foreach ($documents as $i => $document) {
            $currentSlug = $document->getSlug();

            // Проверяем, нужно ли обновлять (если длина больше 120)
            if (mb_strlen($currentSlug) > 120) {
                // Генерируем новый слаг, передавая ID для уникальности
                $newSlug = Slug::generate($document->getName(), (string) $document->getId())->getValue();

                $document->updateSlug($newSlug);
                $updatedCount++;

                // Пакетное сохранение для экономии памяти
                if (($i % $batchSize) === 0) {
                    $this->em->flush();
                }
            }
        }
        $this->em->flush();

        $io->success(sprintf('Успешно обновлено %d слагов.', $updatedCount));

        return Command::SUCCESS;
    }
}
