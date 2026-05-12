<?php

namespace App\Direction\Command\Direction\Category\AssignProduct;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public function __construct(
        #[Assert\Uuid]
        public string $productId,
        #[Assert\Uuid]
        public string $categoryId,
    ) {
    }
}
