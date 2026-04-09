<?php

namespace App\Product\Command\Images\Add;


use Psr\Http\Message\UploadedFileInterface;

class Command
{
    public function __construct(
        public string $productId,
        /** @var array<UploadedFileInterface> */
        public array $uploadedImages,
    ){

    }
}