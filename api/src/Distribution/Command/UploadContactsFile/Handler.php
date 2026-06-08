<?php

declare(strict_types=1);

namespace App\Distribution\Command\UploadContactsFile;

use App\Distribution\Entity\File\File;
use App\Distribution\Entity\File\FileId;
use App\Distribution\Entity\File\FileRepository;
use App\Distribution\Service\ContactImportFileRemoverInterface;
use App\Distribution\Service\ContactImportFileUploaderInterface;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly ContactImportFileUploaderInterface $fileUploader,
        private readonly ContactImportFileRemoverInterface $fileRemover,
        private readonly FileRepository $files,
        private readonly Flusher $flusher,
    ) {
    }

    public function handle(Command $command): void
    {
        $filename = $command->file->getClientFilename();
        if ($filename === null) {
            throw new \DomainException('File name cannot be null.');
        }

        $file = new File(
            $id = FileId::generate(),
            $filename,
            new \DateTimeImmutable(),
        );
        try {
            $this->files->add($file);

            $this->fileUploader->upload($id->getValue(), $command->file);

            $this->flusher->flush();
        } catch (\Exception $e) {
            $this->fileRemover->remove($id->getValue() .DIRECTORY_SEPARATOR. $filename);
            throw new \DomainException('File upload failed.' . $e->getMessage());
        }
    }
}
