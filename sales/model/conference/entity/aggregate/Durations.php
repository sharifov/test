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

        $current = $this->getCurrent();

        if (!$current->isFinished()) {
//            $current->finish($date);
            throw new \DomainException('Current duration is not finished');
        }

        $this->add(Duration::byStart($date));
    }

    public function addEnd(\DateTimeImmutable $date): void
    {
        if ($this->isEmpty()) {
            $this->add(Duration::byFinish($date));
            return;
        }

        $current = $this->getCurrent();

        if ($current->isFinished()) {
            $this->add(Duration::byFinish($date));
            return;
        }

        $current->finish($date);
    }

    public function getValue(): int
    {
        $value = 0;
        foreach ($this->durations as $duration) {
            $value += $duration->getValue();
        }
        return $value;
    }

    public function currentIsActive(): bool
    {
        return $this->getCurrent()->isActive();
    }

    public function isEmpty(): bool
    {
        return empty($this->durations);
    }

    private function getCurrent(): Duration
    {
        $current = end($this->durations);
        if (!$current) {
            throw new \DomainException('Not found current duration.');
        }
        return $current;
    }

    private function add(Duration $duration): void
    {
        $this->durations[] = $duration;
    }

    public function getState(): array
    {
        $state = [];
        foreach ($this->durations as $duration) {
            $state[] = $duration->getState();
        }
        return $state;
    }

    public function getResult(): array
    {
        $result['value'] = $this->getValue();
        foreach ($this->durations as $duration) {
            $result['details'][] = $duration->getResult();
        }
        return $result;
    }
}
