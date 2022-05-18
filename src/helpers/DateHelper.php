<?php

namespace src\helpers;

use DateTime;

/**
 * Class DateHelper
 */
class DateHelper
{
    public static function getMonthList(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July ',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'];
    }

    /**
     * @param int $monthNumber
     * @return mixed|null
     */
    public static function getMonthName(int $monthNumber)
    {
        return self::getMonthList()[$monthNumber] ?? null;
    }

    /**
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function checkDateTime(string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        $createdDateTime = DateTime::createFromFormat($format, $date);
        return $createdDateTime && $createdDateTime->format($format) === $date;
    }

    /**
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function toFormat(string $date, string $format = 'Y-m-d'): string
    {
        return date($format, strtotime($date));
    }
}
