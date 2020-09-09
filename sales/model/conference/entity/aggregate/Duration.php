<?php

namespace sales\model\conference\entity\aggregate;

class Duration
{
    private \DateTimeImmutable $start;

    private bool $started = false;

    private \DateTimeImmutable $finish;

    private bool $finished = false;

    private function __construct()
    {
    }

    public static function byStart(\DateTimeImmutable $date): self
    {
        $duration = new self();
        $duration->start($date);
        return $duration;
    }

    public static function byFinish(\DateTimeImmutable $date): self
    {
        $duration = new self();
        $duration->finish($date);
        return $duration;
    }

    public function finish(\DateTimeImmutable $date): void
    {
        if ($this->isFinished()) {
            throw new \DomainException('Duration is already finished.');
        }
        if ($this->isStarted() && $date < $this->start) {
            throw new \DomainException('Finish date (' . $date->format('Y-m-d H:i:s') . ') must be longer or equal of start date ' . $this->start->format('Y-m-d H:i:s'));
        }
        $this->finish = $date;
        $this->finished = true;
    }

    public function isFinished(): bool
    {
        return $this->finished === true;
    }

    private function start(\DateTimeImmutable $date): void
    {
        if ($this->isStarted()) {
            throw new \DomainException('Duration is already started.');
        }
        $this->start = $date;
        $this->started = true;
    }

    public function isStarted(): bool
    {
        return $this->started === true;
    }

    public function getValue(): int
    {
        if (!$this->isFinished()) {
            throw new \DomainException('Duration not finished. Cant calculate value.');
        }
        if ($this->isStarted()) {
            return $this->finish->getTimestamp() - $this->start->getTimestamp();
        }
        return 0;
    }

    public function isActive(): bool
    {
        return $this->isStarted() && !$this->isFinished();
    }

    public function getState(): array
    {
        $state = [];
        if ($this->isStarted()) {
            $state['start'] = $this->start->format('Y-m-d H:i:s');
        } else {
            $state['start'] = '';
        }
        if ($this->isFinished()) {
            $state['finish'] = $this->finish->format('Y-m-d H:i:s');
        } else {
            $state['finish'] = '';
        }
        return $state;
    }

    public function getResult(): array
    {
        $result['value'] = $this->getValue();
        if ($this->isStarted()) {
            $result['start'] = $this->start->format('Y-m-d H:i:s');
        } else {
            $result['start'] = '';
        }
        if ($this->isFinished()) {
            $result['finish'] = $this->finish->format('Y-m-d H:i:s');
        } else {
            $result['finish'] = '';
        }
        return $result;
    }
}
