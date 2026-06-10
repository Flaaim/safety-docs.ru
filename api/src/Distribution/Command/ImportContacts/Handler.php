<?php

declare(strict_types=1);

namespace App\Distribution\Command\ImportContacts;

use App\Distribution\Entity\Project\Project;
use App\Distribution\Entity\Project\ProjectId;
use App\Distribution\Entity\Project\ProjectRepository;
use App\Distribution\Entity\File\FileId;
use App\Distribution\Entity\File\FileRepository;
use App\Distribution\Service\ContactFileImporterInterface;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly ContactFileImporterInterface $importer,
        private readonly FileRepository               $files,
        private readonly ProjectRepository            $distributions,
        private readonly Flusher                      $flusher,
    ) {}
    public function handle(Command $command): void
    {
        $file = $this->files->findById(new FileId($command->fileId));
        if($file === null) {
            throw new \DomainException('File not found.');
        }

        $distribution = $this->distributions->findById(new ProjectId($command->distributionId));

        if($distribution === null) {
            throw new \DomainException('Distribution not found.');
        }

        $path = $file->getId()->getValue() . DIRECTORY_SEPARATOR . $file->getName();

        $contacts = $this->importer->import($path);
        /** @var Project $distribution */
        $distribution->import($contacts);

        $this->flusher->flush();
    }
}