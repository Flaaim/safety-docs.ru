<?php

declare(strict_types=1);

namespace App\Parser\Command\Launch;


use App\Parser\Service\FetchListDocuments;

final class Handler
{

    public function __construct(
        private readonly FetchListDocuments $fetchListDocuments,
    ){}


    public function handle(Command $command): string
    {
        $listDocuments = ($this->fetchListDocuments)($command->url, $command->cookie);
        return $listDocuments;
    }


}