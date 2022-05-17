<?php

namespace frontend\helpers;

class TimeConverterHelper
{
    /**
     * @param $hours
     * @return int
     */
    public static function hoursToMinutes($hours): int
    {
        $minutes = 0;
        if (strpos($hours, ':') !== false) {
            list($hours, $minutes) = explode(':', $hours);
        }
        return $hours * 60 + $minutes;
    }

    /**
     * @param $minutes
     * @return string
     */
    public static function minutesToHours($minutes): string
    {
        $hours = (int)($minutes / 60);
        $minutes -= $hours * 60;
        return sprintf("%d:%02.0f", $hours, $minutes);
    }

    /**
     * @param $minutes
     * @return string|int
     */
    public static function getMinutesToHours($minutes)
    {
        return $minutes ? static::minutesToHours($minutes) : 0;
    }
}
