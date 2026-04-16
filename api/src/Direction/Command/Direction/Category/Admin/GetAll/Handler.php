<?php

namespace App\Direction\Command\Direction\Category\Admin\GetAll;

use App\Direction\Entity\Category\CategoryRepository;
use App\Direction\Entity\Category\DTO\Admin\CategoryPaginatedDTOMapper;
use App\Direction\Entity\Category\DTO\CategoryDTOMapper;

class Handler
{
    public function __construct(
        private readonly CategoryRepository         $categories,
        private readonly CategoryPaginatedDTOMapper $paginatedDTOMapper,
        private readonly CategoryDTOMapper          $categoryDTOMapper
    ){
    }

    public function handle(Command $command): Response
    {
        $categories = $this->categories->getAllPaginated();

        $categoriesDTO = $this->categoryDTOMapper->mapCollection($categories);

        $categoryPaginatedDTO = $this->paginatedDTOMapper
            ->map($categoriesDTO, $command->page, $command->perPage);

        return Response::fromResult($categoryPaginatedDTO);
    }
}