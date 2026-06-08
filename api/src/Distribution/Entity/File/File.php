<?php

declare(strict_types=1);

namespace App\Distribution\Entity\File;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table('distribution_contacts_files')]
final class File
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'file_id')]
        private FileId $id,
        #[ORM\Column(type: 'string', length: 55)]
        private string $name,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $date,
        #[ORM\Column(type: 'boolean', options: ['default' => false])]
        private bool $complete = false
    ) {}

    public function getId(): FileId
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
    public function isComplete(): bool
    {
        return $this->complete;
    }
    public function complete(): void
    {
        $this->complete = true;
    }
}
