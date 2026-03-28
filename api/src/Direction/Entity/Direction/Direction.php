<?php

namespace App\Direction\Entity\Direction;

use App\Direction\Entity\Category\Category;
use App\Direction\Entity\Category\CategoryId;
use App\Direction\Entity\Slug;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'directions')]
class Direction
{
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'direction', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $categories;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'direction_id', unique: true)]
        private DirectionId $id,
        #[ORM\Column(type: 'string', length: 150)]
        private string $title,
        #[ORM\Column(type: 'string', length: 255)]
        private string $description,
        #[ORM\Column(type: 'text')]
        private string $text,
        #[ORM\Column(type: 'direction_slug', length: 35)]
        private Slug $slug,
        #[ORM\Embedded(class: Breadcrumb::class)]
        private ?Breadcrumb $breadcrumb = null,

    ){
        $this->categories = new ArrayCollection();
    }
    public function getId(): DirectionId
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getText(): string
    {
        return $this->text;
    }
    public function getSlug(): Slug
    {
        return $this->slug;
    }

    public function update(string $title, string $description, string $text, Slug $slug): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->text = $text;
        $this->slug = $slug;
    }
    public function getCategories(): Collection
    {
        return $this->categories;
    }
    public function addCategory(Category $category): void
    {
        foreach ($this->categories as $existingCategory) {
            if($existingCategory->getSlug()->equals($category->getSlug())) {
                throw new \DomainException("Category with slug ".$category->getSlug()->getValue()." is exists.");
            }
        }
        $this->categories->add($category);
    }
    public function isCategoryExist(Slug $categorySlug): bool
    {
        foreach($this->categories as $existingCategory) {
            /** @var Category $existingCategory */
            if($existingCategory->getSlug()->equals($categorySlug)) {
                return true;
            }
        }
        return false;
    }
    public function removeCategory(Slug $slug): void
    {
        foreach ($this->categories as $category) {
            if($category->getSlug()->equals($slug)) {
                $this->categories->removeElement($category);
                return;
            }
        }
        throw new \DomainException('Category not found in this direction.');
    }

    public function canBeDeleted(): bool
    {
        if($this->categories->count() > 0){
            return false;
        }
        return true;
    }
    public function getBreadcrumb(): ?Breadcrumb
    {
        return $this->breadcrumb;
    }

}