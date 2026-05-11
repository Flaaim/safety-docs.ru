<?php

namespace App\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesLoadCommand extends Command
{
    private EntityManagerInterface $em;
    private array $paths;
    public function __construct(EntityManagerInterface $em, array $paths)
    {
        parent::__construct();
        $this->em = $em;
        $this->paths = $paths;
    }

    protected function configure(): void
    {
        $this->setName('fixtures:load')
            ->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<comment>Loading fixtures</comment>');

        $loader = new Loader();

        foreach ($this->paths as $path) {
            $loader->loadFromDirectory($path);
        }

        $executor = new ORMExecutor($this->em, new ORMPurger());

        $executor->execute($loader->getFixtures());

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
