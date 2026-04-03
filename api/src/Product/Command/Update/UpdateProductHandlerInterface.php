<?php

namespace App\Product\Command\Update;

interface UpdateProductHandlerInterface
{
    public function handle(Command $command): void;
    public function getType(string $type): bool;
}