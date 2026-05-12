<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

class RandomFileNameGenerator implements FileNameGeneratorInterface
{
    /**
     *@psalm-suppress PossiblyUnusedReturnValue
     */
    public function generate(UploadedFileInterface $file): string
    {
        $clientFilename = $file->getClientFilename();
        if($clientFilename === null) {
            throw new \DomainException('Client file name cannot be null.');
        }
        $extension = pathinfo($clientFilename, PATHINFO_EXTENSION);
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }
}
