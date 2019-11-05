<?php

namespace sales\services\lead\qcall;

use Yii;

class CalculateDateService
{

    public function calculate(bool $clientTimeEnable, ?string $clientGmt, int $from, int $to): Date
    {
        $fromInterval = new \DateInterval('PT' . $from . 'M');
        $toInterval = new \DateInterval('PT' . $to . 'M');

        if (!$clientTimeEnable || !$clientGmt) {
            return new Date(
                (new \DateTimeImmutable())->add($fromInterval)->format('Y-m-d H:i:s'),
                (new \DateTimeImmutable())->add($toInterval)->format('Y-m-d H:i:s')
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

        $currentTimeClient = (new \DateTimeImmutable())->setTimezone($clientTimeZone);

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

        return new Date(
            $setupFrom->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            $setupTo->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s')
        );
    }

}
