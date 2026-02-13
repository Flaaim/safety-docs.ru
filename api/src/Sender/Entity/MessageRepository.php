<?php

namespace App\Sender\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class MessageRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Message::class);
        $this->repo = $repo;
        $this->em = $em;
    }

    public function create(Message $message): void
    {
        $this->em->persist($message);
    }
}