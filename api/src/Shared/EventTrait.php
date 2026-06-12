<?php

namespace App\Shared;

trait EventTrait
{
    private array $events = [];

    public function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

}