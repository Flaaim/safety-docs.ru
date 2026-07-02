<?php

namespace App\Template\Entity\Category;

use App\Template\Entity\Direction\Direction;
use App\Template\Entity\Document\Document;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'category_id', nullable: true, onDelete: 'CASCADE')]
    private ?Category $parent = null;
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    /** @var Collection<int, Category> $children */
    private Collection $children;
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    /** @var Collection<int, Document> $documents */
    private Collection $documents;
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
        #[ORM\Column(type: 'string', length: 125)]
        private string $slug,
        #[ORM\ManyToOne(targetEntity: Direction::class, inversedBy: 'categories')]
        #[ORM\JoinColumn(name: 'direction_id', referencedColumnName: 'id', nullable: false, onDelete: "RESTRICT")]
        private Direction $direction,
        ?Category $parent = null
    ) {
        $this->children = new ArrayCollection();
        $this->documents = new ArrayCollection();

        if ($parent !== null) {
            if ($parent->getDirection() !== $this->direction) {
                throw new \DomainException('Child category cannot be from different direction.');
            }

            if ($categoryId->equals($parent->getId())) {
                throw new \DomainException('A category cannot be its own parent.');
            }
        }
        $this->parent = $parent;

        if ($this->parent !== null) {
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
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getDirection(): Direction
    {
        return $this->direction;
    }
    /** @return array<Document> */
    public function getDocuments(): array
    {
        return $this->documents->toArray();
    }
    public function update(
        string $title,
        string $description,
        string $text,
        string $slug,
        Direction $direction,
        ?Category $parent = null
    ): void {
        if ($parent !== null) {
            if ($this->categoryId->equals($parent->getId())) {
                throw new \DomainException('A category cannot be its own parent.');
            }
            if (!$this->children->isEmpty()) {
                throw new \DomainException('Cannot move a category with children under another parent. Delete or move its children first.');
            }
            if (!$parent->getDirection()->getId()->equals($direction->getId())) {
                throw new \DomainException('Child category cannot be from different direction.');
            }
        }
        $this->title = $title;
        $this->description = $description;
        $this->text = $text;
        $this->slug = $slug;
        $this->updateDirection($direction);

        $oldParent = $this->parent;

        if ($oldParent !== null && $oldParent !== $parent) {
            $oldParent->removeChild($this);
        }

        $this->parent = $parent;

        if ($this->parent !== null && $oldParent !== $this->parent) {
            $this->parent->addChild($this);
        }
    }
    public function updateDirection(Direction $direction): void
    {
        if ($this->direction->getId()->getValue() !== $direction->getId()->getValue()) {
            $this->direction = $direction;

            foreach ($this->children as $child) {
                $child->updateDirection($direction);
            }
        }
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }
    public function addChild(Category $child): void
    {
        if($this->documents->count() > 0){
            throw new \DomainException('Cannot add a child, because the current category contains documents.');
        }
        $isAlreadyAssigned = $this->children->exists(function (int $key, Category $existingChild) use ($child) {
            return $existingChild->getId()->getValue() === $child->getId()->getValue();
        });
        if ($isAlreadyAssigned) {
            throw new \DomainException('A category child already assigned.');
        }
        $this->children->add($child);
        $child->parent = $this;
    }
    public function removeChild(Category $child): void
    {
        if (
            $this->children->exists(function (int $key, Category $existingChild) use ($child) {
                return $existingChild->getId()->getValue() === $child->getId()->getValue();
            })
        ) {
            $this->children->removeElement($child);
            $child->parent = null;
        }
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
    public function release(): void
    {
        $this->direction->removeCategory($this);
        $this->parent?->removeChild($this);
    }
    public function canBeDeleted(): bool
    {
        if ($this->children->count() > 0 || $this->documents->count() > 0) {
            return false;
        }
        return true;
    }
    public function addDocument(Document $document): void
    {
        if($this->children->count() > 0){
            throw new \DomainException('Cannot add a document, because the current category contains subcategories.');
        }
        $isAlreadyAdded = $this->documents->exists(function (int $key, Document $existingDocument) use ($document) {
            return $existingDocument->getId()->getValue() === $document->getId()->getValue();
        });
        if ($isAlreadyAdded) {
            throw new \DomainException('A document already added in the current category.');
        }
        $this->documents->add($document);
    }
}
