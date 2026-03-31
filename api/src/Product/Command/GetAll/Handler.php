<?php

namespace App\Product\Command\GetAll;

use App\Product\Entity\DTO\ProductDTOMapper;
use App\Product\Entity\DTO\ProductPaginatedDTO;
use App\Product\Entity\DTO\ProductPaginatedDTOMapping;
use App\Product\Entity\ProductRepository;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductPaginatedDTOMapping $mapper,
        private readonly ProductDTOMapper $productDTOMapper,
    ){
    }

    public function handle(Command $command): Response
    {
        $allProducts = $this->products->findAllPaginated();

        $productDTOCollection = $this->productDTOMapper->mapCollection($allProducts);

        $productPaginatedDTO = $this->mapper->map($productDTOCollection, $command->perPage, $command->page);

        return Response::fromResult($productPaginatedDTO);
    }
}