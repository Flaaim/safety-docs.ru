<?php

namespace App\Document\Service\File;

use Psr\Http\Message\UploadedFileInterface;
use Ramsey\Uuid\Uuid;

class RandomFilenameGenerator implements FileNameGeneratorInterface
{
    /**
     *@psalm-suppress PossiblyUnusedReturnValue
     */
    public function generate(UploadedFileInterface $file): string
    {
        $clientFilename = $file->getClientFilename();
        if ($clientFilename === null) {
            throw new \DomainException('Client file name cannot be null.');
        }
        $extension = pathinfo($clientFilename, PATHINFO_EXTENSION);
        return Uuid::uuid4()->toString() . '.' . $extension;
    }
}
