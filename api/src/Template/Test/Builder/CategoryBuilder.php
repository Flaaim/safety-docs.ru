<?php

namespace App\Template\Test\Builder;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Slug;
use App\Product\Entity\Product;

class CategoryBuilder
{
    public CategoryId $categoryId;
    private string $slug;
    private string $title;
    private string $description;
    private string $text;
    private ?Category $parent = null;
    /** @var array<int, Category> $children */
    private array $children = [];

    public function __construct()
    {
        $this->categoryId = new CategoryId('9582c2ff-e788-46f6-94f9-6b7d73b309bd');
        $this->slug = Slug::generate('service')->getValue();
        $this->title = 'Служба охраны труда - образцы документов';
        $this->description = 'Служба охраны труда - образцы документов описание документов';
        $this->text = 'Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда или введите должность';
    }
    public function withCategoryId(CategoryId $categoryId): self
    {
        $clone = clone $this;
        $clone->categoryId = $categoryId;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withSlug(Slug $slug): self
    {
        $clone = clone $this;
        $clone->slug = $slug->getValue();
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withTitle(string $title): self
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withText(string $text): self
    {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }
    public function withProduct(Product $product): self
    {
        $clone = clone $this;
        $clone->product = $product;
        return $clone;
    }
    public function withParent(Category $category): self
    {
        $clone = clone $this;
        $clone->parent = $category;
        return $clone;
    }
    public function withChildren(array $children): self
    {
        $clone = clone $this;
        $clone->children = $children;
        return $clone;
    }
    public function build(Direction $direction): Category
    {
        $category = new Category(
            $this->categoryId,
            $this->title,
            $this->description,
            $this->text,
            $this->slug,
            $direction,
            $this->parent
        );

        if (!empty($this->children)) {
            foreach ($this->children as $child) {
                $category->addChild($child);
            }
        }
        return $category;
    }
}
