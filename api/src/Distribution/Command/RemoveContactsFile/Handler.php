<?php

declare(strict_types=1);

namespace App\Distribution\Command\RemoveContactsFile;

use App\Distribution\Entity\File\FileId;
use App\Distribution\Entity\File\FileRepository;
use App\Distribution\Service\ContactImportFileRemoverInterface;
use App\Flusher;
use Doctrine\DBAL\Connection;

final class Handler
{
    public function __construct(
        private readonly FileRepository $files,
        private readonly ContactImportFileRemoverInterface $remover,
        private readonly Flusher $flusher,
        private readonly Connection $connection
    ) {}

    public function handle(Command $command): void
    {
        $contactsFile = $this->files->findById(new FileId($command->id));
        if ($contactsFile === null) {
            throw new \DomainException('File is not found.');
        }

        $path = $contactsFile->getId()->getValue() . DIRECTORY_SEPARATOR . $contactsFile->getName();
        $this->connection->beginTransaction();
        try {
            if (file_exists($path)) {
                $this->remover->remove($path);
            }

            $this->files->remove($contactsFile);

            $this->flusher->flush();

        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw new \RuntimeException('Failed to remove file and database record: ' . $e->getMessage(), 0, $e);
        }
    }
}