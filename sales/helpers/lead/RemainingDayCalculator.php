<?php

namespace sales\helpers\lead;

use common\models\Airport;

class RemainingDayCalculator
{

    /**
     * @param Airport $airport
     * @param $departure
     * @return mixed|string
     * @throws \Exception
     */
    public static function calculate(Airport $airport, $departure)
    {
        if (!$airport) {
            return 'Airport not found';
        }
        if (!$timeZone = $airport->timezone) {
            $timeZone = 'UTC';
        }

        $nowDep = new \DateTime("now", new \DateTimeZone($timeZone));

        $dateDepartmentNow = new \DateTime($nowDep->format('Y-m-d'));
        $dateDepartment = new \DateTime($departure);

        $interval = $dateDepartmentNow->diff($dateDepartment);

        return $interval->invert ? '-' . $interval->days : $interval->days;
    }

}
