<?php

namespace sales\services\lead\qcall;

use Yii;

class CalculateDateService
{

    public function calculate(
        int $from,
        int $to,
        bool $clientTimeEnable,
        ?string $clientGmt,
        string $time = 'now'
    ): Interval
    {
        $fromInterval = new \DateInterval('PT' . $from . 'M');
        $toInterval = new \DateInterval('PT' . $to . 'M');

        if (!$clientTimeEnable || !$clientGmt) {
            return new Interval(
                (new \DateTimeImmutable($time))->add($fromInterval),
                (new \DateTimeImmutable($time))->add($toInterval)
            );
        }

        $dayTimeHours = new DayTimeHours(Yii::$app->params['settings']['qcall_day_time_hours']);

        if ($dayTimeHours->isEmpty()) {
            Yii::error('qcall_day_time_hours is empty');
        }

        try {
            $clientTimeZone = new \DateTimeZone($clientGmt);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'QCallTimeZoneClient');
            $clientTimeZone = new \DateTimeZone('UTC');
        }

        $currentTimeClient = (new \DateTimeImmutable($time))->setTimezone($clientTimeZone);

        $availableFromClient = $currentTimeClient->setTime($dayTimeHours->startHour, $dayTimeHours->startMinutes);

        $availableToClient = $currentTimeClient->setTime($dayTimeHours->endHour, $dayTimeHours->endMinutes);

        $setupFrom = $currentTimeClient->add($fromInterval);

        if ($setupFrom >= $availableFromClient && $setupFrom <= $availableToClient) {
            $setupTo = $currentTimeClient->add($toInterval);
        } elseif ($setupFrom < $availableFromClient) {
            $setupFrom = $currentTimeClient->setTime($dayTimeHours->startHour, $dayTimeHours->startMinutes);
            $setupTo = $setupFrom->add($toInterval);
        } else {
            $setupFrom = $currentTimeClient->add(new \DateInterval('P1D'))->setTime($dayTimeHours->startHour, $dayTimeHours->startMinutes);
            $setupTo = $setupFrom->add($toInterval);
        }

        return new Interval(
            $setupFrom->setTimezone(new \DateTimeZone('UTC')),
            $setupTo->setTimezone(new \DateTimeZone('UTC'))
        );
    }

}
