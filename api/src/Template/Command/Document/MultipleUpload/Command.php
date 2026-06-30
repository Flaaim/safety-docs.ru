<?php

declare(strict_types=1);

namespace App\Template\Command\Document\MultipleUpload;

use App\Http\Validator\SlimUploadedFile as SlimUploadedFileAssert;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $categoryId,
        #[Assert\NotNull]
        #[Assert\GreaterThan(0)]
        public float $amount,
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type(UploadedFileInterface::class),
            new SlimUploadedFileAssert(
                maxSize: '15M',
                mimeTypes: [
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/msword'
                ],
                extensions: [
                    'docx',
                    'doc'
                ]
            )
        ])]
        /** @var array<UploadedFileInterface> $files */
        public array $files,
    ) {
    }
}
