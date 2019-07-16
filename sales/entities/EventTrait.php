<?php

namespace sales\entities;

trait EventTrait
{
    private $events = [];

    protected function recordEvent($event, string $key = null): void
    {
        if ($key === null) {
            $this->events[] = $event;
        } else {
            $this->events[$key] = $event;
        }
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}