<?php

namespace sales\model\conference\entity\aggregate;

/**
 * Class Durations
 *
 * @property Duration[] $durations
 */
class Durations
{
    private array $durations = [];

    public function addStart(\DateTimeImmutable $date): void
    {
        if ($this->isEmpty()) {
            $this->add(Duration::byStart($date));
            return;
        }

        if (($duration = $this->getCurrent()) && !$duration->isFinished()) {
            throw new \DomainException('Last duration is not finished');
        }

        $this->add(Duration::byStart($date));
    }

    public function addEnd(\DateTimeImmutable $date): void
    {
        if ($this->isEmpty()) {
            $this->add(Duration::byEnd($date));
            return;
        }

        if (($duration = $this->getCurrent()) && $duration->isFinished()) {
            throw new \DomainException('Last duration is already finished');
        }

        $duration->end($date);
    }

    public function isEmpty(): bool
    {
        return empty($this->durations);
    }

    public function getValue(): int
    {
        $value = 0;
        foreach ($this->durations as $duration) {
            $value += $duration->getValue();
        }
        return $value;
    }

    public function isStarted(): bool
    {
        if (!$current = $this->getCurrent()) {
            return false;
        }
        return $current->isStarted();
    }

    private function getCurrent(): ?Duration
    {
        return end($this->durations);
    }

    private function add(Duration $duration): void
    {
        $this->durations[] = $duration;
    }
}
