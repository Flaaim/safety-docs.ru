<?php

namespace App\Direction\Entity\Direction;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Embeddable]
class Breadcrumb
{
    public function __construct(
        #[ORM\Column(type:'string', length: 35, nullable: true)]
        private string $slug,
        #[ORM\Column(type:'string', length: 150, nullable: true)]
        private string $title,
    ){
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
}