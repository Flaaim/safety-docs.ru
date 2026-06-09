<?php

namespace App\Shared\Domain\Event;

trait EventTrait
{
    private array $events = [];

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    protected function recordEvent(object $event): void
    {
        $this->events[] = $event;
    }
}