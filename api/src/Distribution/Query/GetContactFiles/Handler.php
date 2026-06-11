<?php

declare(strict_types=1);

namespace App\Distribution\Query\GetContactFiles;

use App\Distribution\Entity\File\DTO\FileDTOMapper;
use App\Distribution\Entity\File\DTO\FilePaginatedDTO;
use App\Distribution\Entity\File\FileRepository;

final class Handler
{
    public function __construct(
        private readonly FileRepository $files,
        private readonly FileDTOMapper $fileDTOMapper,
    ) {
    }
    public function handle(Command $command): Response
    {
        $page = $command->page;
        $perPage = $command->perPage;
        $paginatedResult = $this->files->findPaginated($page, $perPage);

        $filesDTOCollection = $this->fileDTOMapper->mapCollection($paginatedResult['items']);

        $total = $paginatedResult['total'];
        $totalPages = (int)ceil($total / $command->perPage) ?: 1;

        $filePaginatedDTO = new FilePaginatedDTO(
            $filesDTOCollection,
            $total,
            $page,
            $perPage,
            $totalPages,
        );

        return Response::fromResult($filePaginatedDTO);
    }
}
