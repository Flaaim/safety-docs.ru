<?php

namespace App\Direction\Command\Direction\Category\Admin\GetAll;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\Admin\CategoryPaginatedDTOMapping;

class Handler
{
    public function __construct(
        private readonly CategoryRepository          $categories,
        private readonly CategoryPaginatedDTOMapping $dtoMapping
    ){
    }

    public function handle(Command $command): Response
    {
        $categories = $this->categories->getAllPaginated();

        $categoryPaginatedDTO = $this->dtoMapping->map($categories, $command->page, $command->perPage);

        return Response::fromResult($categoryPaginatedDTO);
    }
}