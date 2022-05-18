<?php

namespace frontend\helpers;

class TimeConverterHelper
{
    /**
     * @param string $hours
     * @format $hours - H:i
     * @example $minutes = TimeConverterHelper::hoursToMinutes('01:42');
     * @return int
     */
    public static function hoursToMinutes(string $hours): int
    {
        $minutes = 0;
        if (strpos($hours, ':') !== false) {
            list($hours, $minutes) = explode(':', $hours);
        }
        return $hours * 60 + $minutes;
    }

    /**
     * @param int $minutes
     * @return string|0
     */
    public static function minutesToHours(int $minutes): string
    {
        if ($minutes <= 0) {
            return 0;
        }
        $hours = (int)($minutes / 60);
        $minutes -= $hours * 60;
        return sprintf("%d:%02.0f", $hours, $minutes);
    }
}
