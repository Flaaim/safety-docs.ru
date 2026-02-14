<?php

namespace App\Product\Command\GetAll;

use App\Product\Entity\DTO\ProductPaginatedDTO;
use App\Product\Entity\ProductRepository;

class Handler
{
    public function __construct(
        private readonly ProductRepository $products
    ){
    }

    public function handle(Command $command): Response
    {
        $allProducts = $this->products->findAllPaginated();
        $total = count($allProducts);
        $perPage = $command->perPage;

        $offset = ($command->page - 1) * $perPage;
        $products = array_slice($allProducts, $offset, $perPage);

        $productPaginatedDTO = new ProductPaginatedDTO(
            $products,
            $total,
            $command->page,
            $perPage,
            (int) ceil($total / $perPage) ?: 1
        );

        return Response::fromResult($productPaginatedDTO);
    }
}