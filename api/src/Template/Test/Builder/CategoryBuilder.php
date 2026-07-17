<?php

namespace App\Template\Test\Builder;

use App\Template\Entity\Category\Category;
use App\Template\Entity\Category\CategoryId;
use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Document\Document;
use App\Template\Entity\Slug;

class CategoryBuilder
{
    public CategoryId $categoryId;
    private string $slug;
    private string $title;
    private string $description;
    private string $text;
    /** @var Document[] $documents */
    private array $documents;
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
        $this->documents = [];
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
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withDocuments(array $documents): self
    {
        $clone = clone $this;
        $clone->documents = $documents;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withParent(Category $category): self
    {
        $clone = clone $this;
        $clone->parent = $category;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
    public function withChildren(array $children): self
    {
        $clone = clone $this;
        $clone->children = $children;
        return $clone;
    }
    /** @psalm-suppress PossiblyUnusedMethod */
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

        if (!empty($this->documents) && empty($this->children)) {
            foreach ($this->documents as $document) {
                $category->addDocument($document);
            }
        }

        if (!empty($this->children) && empty($this->documents)) {
            foreach ($this->children as $child) {
                $category->addChild($child);
            }
        }
        return $category;
    }
}
