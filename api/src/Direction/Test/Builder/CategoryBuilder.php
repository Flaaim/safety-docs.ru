<?php

namespace App\Direction\Test\Builder;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Slug;
use App\Product\Entity\Product;

class CategoryBuilder
{
    public CategoryId $categoryId;
    private Slug $slug;
    private string $title;
    private string $description;
    private string $text;
    private ?Product $product = null;
    private ?Category $parent = null;
    /** @var array<int, Category> $children */
    private array $children;

    public function __construct()
    {
        $this->categoryId = new CategoryId('9582c2ff-e788-46f6-94f9-6b7d73b309bd');
        $this->slug = new Slug('service');
        $this->title = 'Служба охраны труда - образцы документов';
        $this->description = 'Служба охраны труда - образцы документов описание документов';
        $this->text = 'Оцените численность штата. Если в организации более 50 человек — создайте службу охраны труда или введите должность специалиста, оформив приказ и утвердив Положение о службе';
    }
    public function withCategoryId(CategoryId $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    public function withSlug(Slug $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
    public function withTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
    public function withDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function withText(string $text): self
    {
        $this->text = $text;
        return $this;
    }
    function withProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }
    function withParent(Category $category): self
    {
        $this->parent = $category;
        return $this;
    }
    function withChildren(array $children): self
    {
        $this->children = $children;
        return $this;
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

        if($this->product !== null) {
            $category->assignProduct($this->product);
        }

        if(!empty($this->children)) {
            foreach($this->children as $child){
                $category->assignChildren($child);
            }
        }
        return $category;
    }
}