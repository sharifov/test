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

    public static function getDateTimeImmutableUTC(string $date): \DateTimeImmutable
    {
        return (new \DateTimeImmutable($date, new \DateTimeZone('UTC')));
    }

    public static function toFormatByUTC(string $date, string $format = 'Y-m-d'): string
    {
        return self::getDateTimeImmutableUTC($date)->format($format);
    }

    public static function getDifferentInDaysByDatesUTC(string $startDate, string $endDate): int
    {
        $startDateTime = self::getDateTimeImmutableUTC($startDate);
        $endDateTime = self::getDateTimeImmutableUTC($endDate);

        return (int)$startDateTime->diff($endDateTime)->format('%a');
    }

    public static function getDifferentInMinutesByDatesUTC(string $startDate, string $endDate): int
    {
        $startDateTime = self::getDateTimeImmutableUTC($startDate);
        $endDateTime = self::getDateTimeImmutableUTC($endDate);

        $diff = $startDateTime->diff($endDateTime)->format('%a-%h-%i-%s');
        $diffParts = explode('-', $diff);

        return ((int)$diffParts[0] * 24 * 60) + ((int)$diffParts[1] * 60) + (int)$diffParts[2];
    }

    public static function getDifferentInSecondsByDatesUTC(string $startDate, string $endDate): int
    {
        $startDateTime = self::getDateTimeImmutableUTC($startDate);
        $endDateTime = self::getDateTimeImmutableUTC($endDate);

        $diff = $startDateTime->diff($endDateTime)->format('%a-%h-%i-%s');
        $diffParts = explode('-', $diff);

        return ((int)$diffParts[0] * 24 * 60 * 60) + ((int)$diffParts[1] * 60 * 60) + ((int)$diffParts[2] * 60) + (int)$diffParts[3];
    }

    public static function getDateTimeWithAddedMinutesUTC(string $date, int $minutes, string $format = 'Y-m-d H:i:s'): string
    {
        $dateTime = self::getDateTimeImmutableUTC($date);

        return $dateTime->modify("+{$minutes} minutes")->format($format);
    }

    /**
     * Checks if the date is within the range of the other two
     *
     * @param string $comparableDate
     * @param string $startDate
     * @param string $endDate
     * @return bool
     */
    public static function isDateInTheRangeOtherTwoDates(string $comparableDate, string $startDate, string $endDate): bool
    {
        $compD = strtotime($comparableDate);
        $startD = strtotime($startDate);
        $endD = strtotime($endDate);

        $result = $compD >= $startD && $compD <= $endD;

        return $result;
    }
}
