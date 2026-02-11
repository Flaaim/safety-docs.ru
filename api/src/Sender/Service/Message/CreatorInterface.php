<?php

namespace App\Sender\Service\Message;

use App\Sender\Entity\Recipient;
use Symfony\Component\Mime\Email;

interface CreatorInterface
{
    public function create(Recipient $recipient): Email;
}