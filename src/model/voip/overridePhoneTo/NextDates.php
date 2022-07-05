<?php

namespace src\model\voip\overridePhoneTo;

class NextDates
{
    private \DateTimeImmutable $from;
    private \DateTimeImmutable $to;
    private \DateTimeImmutable $end;
    private int $stepHours;

    public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $end, int $stepHours)
    {
        $this->stepHours = $stepHours;
        $this->from = $from;
        $this->to = $this->from->add(\DateInterval::createFromDateString($this->stepHours . ' hour'));
        $this->end = $end;
        if ($this->to > $this->end) {
            $this->to = $this->end;
        }
    }

    public function getFromDate(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getToDate(): \DateTimeImmutable
    {
        return $this->to;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->end;
    }

    public function isExpired(): bool
    {
        return $this->from >= $this->end;
    }

    public function getStepHours(): int
    {
        return $this->stepHours;
    }
}
