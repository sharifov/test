<?php

namespace frontend\helpers;



/**
 * Class OutHelper
 */
class OutHelper
{
    /**
     * @param int $seconds
     * @return string
     * @throws \Exception
     */
    public static function diffHoursMinutes(int $seconds): string
    {
        $from = new \DateTime("@$seconds", new \DateTimeZone('UTC'));
        $to = new \DateTime('now', new \DateTimeZone('UTC'));
        $secondDifference = ($to->getTimestamp() - $from->getTimestamp());
        $hoursDiff = ($secondDifference / 3600) % 3600;
        $interval = $from->diff($to);
        $hours = $hoursDiff < 10 ? '0' . $hoursDiff : $hoursDiff;
        $minutes = $interval->i < 10 ? '0' . $interval->i : $interval->i;

        return '<span title="hours">' . $hours . '</span>:<span title="minutes">' . $minutes . '</span>';
    }
}
