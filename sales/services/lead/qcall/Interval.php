<?php

namespace sales\services\lead\qcall;

/**
 * Class Interval
 *
 * @property \DateTimeImmutable $from
 * @property \DateTimeImmutable $to
 */
class Interval
{

    private $from;
    private $to;

    /**
     * @param \DateTimeImmutable $from
     * @param \DateTimeImmutable $to
     */
    public function __construct(\DateTimeImmutable $from, \DateTimeImmutable $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param string $format
     * @return string
     */
    public function fromFormat(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->from->format($format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function toFormat(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->to->format($format);
    }

}
