<?php

namespace App\Sender\Command\DeliverMessage\Create;

use App\Sender\Entity\Recipient;

class Command
{
    public function __construct(
        public Recipient $recipient,
    ){
    }
}