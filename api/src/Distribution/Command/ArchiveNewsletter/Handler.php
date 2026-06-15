<?php

declare(strict_types=1);

namespace App\Distribution\Command\ArchiveNewsletter;

use App\Distribution\Entity\Newsletter\NewsletterId;
use App\Distribution\Entity\Newsletter\NewsletterRepository;
use App\Flusher;

final class Handler
{
    public function __construct(
        private readonly NewsletterRepository $newsletters,
        private readonly Flusher $flusher
    ) {
    }

    public function handle(Command $command): void
    {
        $newsletter = $this->newsletters->findById(new NewsletterId($command->newsletterId));

        if ($newsletter === null) {
            throw new \DomainException('Newsletter not found');
        }
        $newsletter->archive();

        $this->flusher->flush();
    }
}
