<?php

namespace App\Product\Command\Images\Add;

use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;
use App\Product\Service\File\FileUploaderInterface;
use App\Shared\Domain\ValueObject\FileSystem\FileSystemPathInterface;
use Psr\Http\Message\UploadedFileInterface;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly Flusher $flusher,
        private readonly FileUploaderInterface $fileUploader,
        private readonly FileSystemPathInterface $imageSystemPath,
    ){
    }

    public function handle(Command $command): void
    {
        $product = $this->products->findById(new ProductId($command->productId));

        if(null === $product){
            throw new \DomainException('Product not found.');
        }

        $path = $this->imageSystemPath->getValue() . DIRECTORY_SEPARATOR . $product->getId()->getValue();

        foreach ($command->uploadedImages as $uploadedImage) {
            if(!$uploadedImage instanceof UploadedFileInterface || $uploadedImage->getError() !== UPLOAD_ERR_OK){
               throw new \DomainException('Error while uploading image file.');
            }

            $this->fileUploader->upload($path, $uploadedImage);

            $product->attachImage($uploadedImage->getClientFilename());
        }

        $this->flusher->flush();
    }
}