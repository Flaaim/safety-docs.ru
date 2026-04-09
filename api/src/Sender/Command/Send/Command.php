<?php

namespace App\Sender\Command\Send;

use App\Sender\Entity\Recipient;

class Command
{
    public function __construct(
        public Recipient $recipient,
    ){
    }
}