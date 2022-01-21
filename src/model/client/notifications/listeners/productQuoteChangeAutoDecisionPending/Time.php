<?php

namespace src\model\client\notifications\listeners\productQuoteChangeAutoDecisionPending;

/**
 * Class Time
 *
 * @property \DateTimeImmutable|null $start
 * @property \DateTimeImmutable|null $end
 * @property int $fromHours
 * @property int $toHours
 */
class Time
{
    public ?\DateTimeImmutable $start;
    public ?\DateTimeImmutable $end;
    public int $fromHours;
    public int $toHours;

    public function __construct(?\DateTimeImmutable $start, ?\DateTimeImmutable $end, int $fromHours, int $toHours)
    {
        $this->start = $start;
        $this->end = $end;
        $this->fromHours = $fromHours;
        $this->toHours = $toHours;
    }
}
