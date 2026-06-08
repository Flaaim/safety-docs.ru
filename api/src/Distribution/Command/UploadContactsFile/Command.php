<?php

declare(strict_types=1);

namespace App\Distribution\Command\UploadContactsFile;

use App\Http\Validator\SlimUploadedFile as SlimUploadedFileAssert;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraints as Assert;
final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[SlimUploadedFileAssert(
            maxSize: '15M',
            mimeTypes: [
                'text/csv',
            ],
            extensions: [
                'csv',
            ]
        )]
        public UploadedFileInterface $file,
    ) {
    }
}
