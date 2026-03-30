<?php

namespace App\Direction\Command\Direction\Category\AssignProduct;

class Command
{
    public function __construct(
        public string $productId,
        public string $categoryId,
    ){
    }
}