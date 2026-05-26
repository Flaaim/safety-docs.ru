<?php

namespace App\Product\Command\GetAll;

use App\Product\Entity\DTO\ProductDTOMapper;
use App\Product\Entity\DTO\ProductPaginatedDTO;
use App\Product\Entity\ProductRepository;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductDTOMapper $productDTOMapper,
    ) {
    }

    public function handle(Command $command): Response
    {
        $paginatedResult = $this->products->findPaginated($command->page, $command->perPage);

        $productDTOCollection = $this->productDTOMapper->mapCollection($paginatedResult['items']);

        $total = $paginatedResult['total'];
        $totalPages = (int)ceil($total / $command->perPage) ?: 1;

        $productPaginatedDTO = new ProductPaginatedDTO(
            $productDTOCollection,
            $total,
            $command->page,
            $command->perPage,
            $totalPages
        );

        return Response::fromResult($productPaginatedDTO);
    }
}
