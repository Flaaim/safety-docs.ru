<?php

namespace App\Direction\Entity\Category;

use App\Direction\Entity\Direction\Direction;
use App\Direction\Entity\Slug;
use App\Product\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\OneToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Product $product = null;
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'category_id', nullable: true, onDelete: 'CASCADE')]
    private ?Category $parent = null;
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    /** @var Collection<int, Category> $children */
    private Collection $children;
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'category_id', unique: true)]
        private CategoryId $categoryId,
        #[ORM\Column(type: 'string', length: 150)]
        private string $title,
        #[ORM\Column(type: 'string', length: 255)]
        private string $description,
        #[ORM\Column(type: 'text')]
        private string $text,
        #[ORM\Column(type: 'category_slug', length: 125)]
        private Slug $slug,
        #[ORM\ManyToOne(targetEntity: Direction::class, inversedBy: 'categories')]
        #[ORM\JoinColumn(name: 'direction_id', referencedColumnName: 'id', nullable: false, onDelete: "RESTRICT")]
        private Direction $direction,
        ?Category $parent = null
    ){
        $this->children = new ArrayCollection();

        if($parent !== null){
            if($parent->getDirection() !== $this->direction){
                throw new \DomainException('Child category cannot be from different direction.');
            }

            if($categoryId->equals($parent->getId())){
                throw new \DomainException('A category cannot be its own parent.');
            }
        }
        $this->parent = $parent;

        if($this->parent !== null){
            $this->parent->addChild($this);
        }

        $direction->addCategory($this);
    }
    public function getId(): CategoryId
    {
        return $this->categoryId;
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
    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function update(
        string $title,
        string $description,
        string $text,
        Slug $slug,
        Direction $direction,
        ?Category $parent = null
    ): void
    {
        if($parent !== null){
            if($this->categoryId->equals($parent->getId())){
                throw new \DomainException('A category cannot be its own parent.');
            }
            if(!$this->children->isEmpty()){
                throw new \DomainException('Cannot move a category with children under another parent. Delete or move its children first.');
            }
            if(!$parent->getDirection()->getId()->equals($direction->getId())){
                throw new \DomainException('Child category cannot be from different direction.');
            }
        }
        $this->title = $title;
        $this->description = $description;
        $this->text = $text;
        $this->slug = $slug;
        $this->updateDirection($direction);
        $this->parent = $parent;

        if ($this->parent !== null) {
            $this->parent->addChild($this);
        }
    }
    private function appendDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }
    public function updateDirection(Direction $direction): void
    {
        if($this->direction->getId()->getValue() !== $direction->getId()->getValue()) {
            if($this->direction->isCategoryExist($this->slug)) {
                $this->direction->removeCategory($this->slug);
                $this->appendDirection($direction);
                $this->direction->addCategory($this);
            }

            foreach ($this->children as $child) {
                $child->updateDirection($direction);
            }
        }
    }
    public function assignProduct(Product $product): void
    {
        if($this->product !== null) {
            throw new \DomainException('Product already assigned. You must delete it first.');
        }
        if(!$this->isChild()){
            throw new \DomainException('Product can be assigned to only child category.');
        }
        $this->product = $product;
    }
    public function refuseProduct(): void
    {
        if($this->product === null) {
            throw new \DomainException('Product not assigned.');
        }
        $this->product = null;
    }
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    public function getParent(): ?Category
    {
        return $this->parent;
    }
    public function addChild(Category $child): void
    {
        $isAlreadyAssigned = $this->children->exists(function (int $key, Category $existingChild) use ($child) {
            return $existingChild->getId()->getValue() === $child->getId()->getValue();
        });
        if($isAlreadyAssigned){
            throw new \DomainException('A category child already assigned.');
        }
        $this->children->add($child);
    }
    /**
     * @return array<Category>
     */
    public function getChildren(): array
    {
        return $this->children->toArray();
    }
    public function isChild(): bool
    {
        return $this->parent !== null;
    }

}