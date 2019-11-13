<?php

namespace sales\services\lead\qcall;

/**
 * Class DayTimeHours
 *
 * @property $startHour
 * @property $startMinutes
 * @property $endHour
 * @property $endMinutes
 */
class DayTimeHours
{
    public $startHour;
    public $startMinutes;

    public $endHour;
    public $endMinutes;

    /**
     * ex. 9:00;21:00
     *
     * @param string $interval
     */
    public function __construct(string $interval)
    {
        preg_match_all('/(\d{0,2}):(\d{0,2});(\d{0,2}):(\d{0,2})/', $interval, $matches);
        $this->startHour = isset($matches[1][0]) ? (int)$matches[1][0] : 0;
        $this->startMinutes = isset($matches[2][0]) ? (int)$matches[2][0] : 0;
        $this->endHour = isset($matches[3][0]) ? (int)$matches[3][0] : 0;
        $this->endMinutes = isset($matches[4][0]) ? (int)$matches[4][0] : 0;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->startHour && !$this->startMinutes && !$this->endHour &&! $this->endMinutes;
    }

    /**
     * @param $value
     * @return string
     */
    private static function format($value): string
    {
        if (strlen($value) === 1) {
            return '0' . $value;
        }
        return $value;
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return self::format($this->startHour) . ':' . self::format($this->startMinutes) . ':00';
    }

    /**
     * @return string
     */
    public function getEnd(): string
    {
        return self::format($this->endHour) . ':' . self::format($this->endMinutes) . ':00';
    }

}
