<?php

namespace App\Product\Command\Update;

use App\Http\Validator\SlimUploadedFile as SlimUploadedFileAssert;
use App\Product\Entity\FormatDocument;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        #[Assert\NotBlank]
        public string $productId,
        #[Assert\Length(min: 5, max: 255)]
        public string $name,
        #[Assert\NotBlank]
        public string $cipher,
        #[Assert\Positive]
        public float $amount,
        #[Assert\NotBlank]
        public string $slug,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'Y-d-m')]
        public string $updatedAt,
        #[Assert\GreaterThan(0)]
        public int $totalDocuments,
        #[Assert\NotBlank]
        #[Assert\Choice(
            callback: [FormatDocument::class, 'getValues'],
            multiple: true,
            message: 'One or more selected document formats are invalid.')]
        public array $formatDocuments,
        #[SlimUploadedFileAssert(
            maxSize: '15M',
            mimeTypes: [
                'application/vnd.rar',
                'application/x-rar-compressed',
                'application/x-rar',
                'application/octet-stream',
                'application/x-compressed'
            ],
            extensions: [
                'rar',
            ]
        )]
        public ?UploadedFileInterface $file = null,
    ){}
}