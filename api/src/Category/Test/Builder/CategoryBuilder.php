<?php

namespace App\Category\Test\Builder;

use App\Category\Entity\Category;
use App\Category\Entity\CategoryId;
use App\Category\Entity\Slug;
use App\Direction\Test\Builder\DirectionBuilder;

class CategoryBuilder
{
    public CategoryId $categoryId;
    private Slug $slug;
    private string $title;
    private string $description;
    private string $text;

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

    public function build(): Category
    {
        return new Category(
            $this->categoryId,
            $this->title,
            $this->description,
            $this->text,
            $this->slug,
            (new DirectionBuilder())->build()
        );
    }
}