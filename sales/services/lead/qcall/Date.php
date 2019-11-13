<?php

namespace sales\services\lead\qcall;

/**
 * Class Date
 *
 * @property $from
 * @property $to
 */
class Date
{

    public $from;
    public $to;

    /**
     * @param $from
     * @param $to
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

}
