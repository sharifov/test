<?php

namespace sales\services\lead\qcall;

/**
 * Class Config
 *
 * @property int $status
 * @property int $callCount
 */
class Config
{
    public $status;
    public $callCount;

    /**
     * @param int $status
     * @param int $callCount
     */
    public function __construct(int $status, int $callCount)
    {
        $this->status = $status;
        $this->callCount = $callCount;
    }
}
