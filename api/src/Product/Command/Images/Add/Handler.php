<?php

namespace App\Product\Command\Images\Add;

use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileNameGeneratorInterface;
use App\Product\Service\File\FileUploaderInterface;
use Psr\Http\Message\UploadedFileInterface;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly FileUploaderInterface $uploader,
        private readonly FileNameGeneratorInterface $nameGenerator
    ){
    }

    public function handle(Command $command): void
    {
        $product = $this->products->findById(new ProductId($command->productId));

        if(null === $product){
            throw new \DomainException('Product not found.');
        }

        foreach ($command->uploadedImages as $uploadedImage) {
            if(!$uploadedImage instanceof UploadedFileInterface || $uploadedImage->getError() !== UPLOAD_ERR_OK){
               throw new \DomainException('Error while uploading image.');
            }

            $filename = $this->uploader->upload($product->getId()->getValue(), $uploadedImage, $this->nameGenerator);

            $product->attachImage($filename);
        }

        $this->flusher->flush();
    }
}