<?php

namespace App\Product\Command\Update;

use App\Http\Validator\SlimUploadedFile as SlimUploadedFileAssert;
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
        public string $path,
        #[Assert\NotBlank]
        public string $slug,
        #[Assert\NotBlank]
        #[Assert\DateTime(format: 'd.m.Y')]
        public string $updatedAt,
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
        public ?UploadedFileInterface $file,
    ){}
}