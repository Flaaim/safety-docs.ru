<?php

namespace App\Product\Command\Images\Add;

use Symfony\Component\Validator\Constraints as Assert;
use Psr\Http\Message\UploadedFileInterface;
use App\Http\Validator\SlimUploadedFile as SlimUploadedFileAssert;

class Command
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $productId,
        #[Assert\NotBlank]
        #[Assert\All(
            new SlimUploadedFileAssert(
                maxSize: '15M',
                mimeTypes: [
                    'image/jpeg',
                    'image/png',
                ],
                extensions: [
                    'jpg', 'png', 'jpeg'
                ]
            ),
        )]
        /** @var array<UploadedFileInterface> */
        public array $uploadedImages,
    ){

    }
}