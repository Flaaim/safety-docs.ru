<?php

namespace App\Distribution\Entity\Newsletter;

enum NewsletterStatus: string
{
    case Created = 'created';
    case Processed = 'processed';
    case Completed = 'completed';
    case Failed = 'failed';
    case Archived = 'archived';
}
