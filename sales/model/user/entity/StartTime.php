<?php

namespace sales\model\user\entity;

/**
 * Class StartTime
 *
 * @property $hour
 * @property $minute
 * @property $second
 */
class StartTime
{
    public $hour;
    public $minute;
    public $second;

    /**
     * @param string $startTime
     */
    public function __construct(string $startTime)
    {
        $time = explode(':', $startTime);
        if (isset($time[0], $time[1], $time[2])) {
            $this->hour = $time[0];
            $this->minute = $time[1];
            $this->second = $time[2];
        }
    }
}
