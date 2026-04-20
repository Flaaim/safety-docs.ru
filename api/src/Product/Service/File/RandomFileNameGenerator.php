<?php

namespace App\Product\Service\File;

use Psr\Http\Message\UploadedFileInterface;

class RandomFileNameGenerator implements FileNameGeneratorInterface
{

    public function generate(UploadedFileInterface $file): string
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }
}