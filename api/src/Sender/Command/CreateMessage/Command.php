<?php

namespace App\Sender\Command\CreateMessage;

use App\Sender\Entity\Recipient;

class Command
{
    public function __construct(
        public Recipient $recipient,
    ){
    }
}