<?php

namespace sales\model\conference\entity\aggregate;

class Duration
{
    private ?\DateTimeImmutable $start = null;

    private bool $finished = false;

    private int $value = 0;

    private function __construct()
    {
    }

    public static function byStart(\DateTimeImmutable $date): self
    {
        $duration = new self();
        $duration->start = $date;
        return $duration;
    }

    public static function byEnd(\DateTimeImmutable $date): self
    {
        $duration = new self();
        $duration->end($date);
        return $duration;
    }

    public function isStarted(): bool
    {
        return $this->start !== null;
    }

    public function end(\DateTimeImmutable $date): void
    {
        if (!$this->isStarted()) {
            $this->start = $date;
        }
        $this->value = abs(($date->diff($this->start))->format('%s'));
        $this->finished = true;
    }

    public function isFinished(): bool
    {
        return $this->finished === true;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
