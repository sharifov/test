<?php

namespace sales\model\client\notifications\listeners\productQuoteChange;

/**
 * Class Time
 *
 * @property \DateTimeImmutable|null $start
 * @property \DateTimeImmutable|null $end
 */
class Time
{
    public ?\DateTimeImmutable $start;
    public ?\DateTimeImmutable $end;

    public function __construct(?\DateTimeImmutable $start, ?\DateTimeImmutable $end)
    {
        $this->start = $start;
        $this->end = $end;
    }
}
