<?php

namespace modules\rentCar\src\helpers;

use common\models\Airports;
use DateInterval;
use DatePeriod;
use DateTime;

/**
 * Class RentCarHelper
 */
class RentCarHelper
{
    /**
     * @param string $timeFormat
     * @param string $duration
     * @param string $start
     * @param string $end
     * @return array
     * @throws \Exception
     */
    public static function listTime(
        string $timeFormat = 'H:i',
        string $duration = 'PT15M',
        string $start = '00:00:00',
        string $end = '23:59:00'
    ): array {
        $startTime = new DateTime($start);
        $endTime = new DateTime($end);
        $interval = new DateInterval($duration);
        $period = new DatePeriod($startTime, $interval, $endTime);
        $result = [];
        foreach ($period as $dt) {
            $time = $dt->format($timeFormat);
            $result[$time] = $time;
        }
        return $result;
    }

    public static function locationByIata(?string $iata): string
    {
        if ($iata && $airport = Airports::findByIata($iata)) {
            return $airport->city . ', ' . $airport->country . ' (' . $iata . ')';
        }
        return '';
    }
}
