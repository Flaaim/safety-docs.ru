<?php

namespace App\Direction\Command\Direction\Category\AssignProduct;

use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Category\CategoryRepository;
use App\Flusher;
use App\Product\Entity\ProductId;
use App\Product\Entity\ProductRepository;

class Handler
{

    public function __construct(
        private ProductRepository $products,
        private CategoryRepository $categories,
        private Flusher $flusher
    ){
    }
    public function handle(Command $command): void
    {

        $category = $this->categories->findById(new CategoryId($command->categoryId));

        if(null === $category) {
            throw new \DomainException('Category not found.');
        }

        $product = $this->products->findById(new ProductId($command->productId));

        if(null === $product) {
            throw new \DomainException('Product not found.');
        }

        $category->assignProduct($product);

        $this->flusher->flush();
    }
}