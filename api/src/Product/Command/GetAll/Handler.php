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
    ){
    }

    public function handle(Command $command): Response
    {
        $allProducts = $this->products->findAllPaginated();

        $productPaginatedDTO = $this->mapper->map($allProducts, $command->perPage, $command->page);

        return Response::fromResult($productPaginatedDTO);
    }
}