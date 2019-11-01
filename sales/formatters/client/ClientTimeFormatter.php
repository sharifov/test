<?php

namespace sales\formatters\client;

use Yii;
use sales\services\lead\qcall\DayTimeHours;
use yii\helpers\Html;

class ClientTimeFormatter
{

    /**
     * @param \DateTime|null $dt
     * @param bool $offsetGmt
     * @return string
     */
    public static function format(?\DateTime $dt, $offsetGmt = false): string
    {
        if (!$dt) {
            return '';
        }
        return '<b title="TZ (' . $dt->format("P") . ')' . (!$offsetGmt ? ' by IATA' : '') . '"><i class="fa fa-clock-o' . ($offsetGmt ? ' success' : '') . '"></i> ' . Html::encode($dt->format('H:i')) . '</b>';
    }

    /**
     * @param \DateTime|null $dt
     * @param bool $offsetGmt
     * @return string
     */
    public static function dayHoursFormat(?\DateTime $dt, $offsetGmt = false): string
    {
        if (!$dt) {
            return '';
        }

        $dayTimeHours = new DayTimeHours(Yii::$app->params['settings']['qcall_day_time_hours']);

        if ($dayTimeHours->isEmpty()) {
            Yii::error('qcall_day_time_hours is empty');
        }

        if (
            $dayTimeHours->startHour > (int)$dt->format('H')
            || $dayTimeHours->endHour < (int)$dt->format('H')
            || ($dayTimeHours->startHour === (int)$dt->format('H') && $dayTimeHours->startMinutes > (int)$dt->format('i'))
            || ($dayTimeHours->endHour === (int)$dt->format('H') && $dayTimeHours->endMinutes < (int)$dt->format('i'))
        ) {
            return '<b style="color:red" title="TZ (' . $dt->format("P") . ')' . (!$offsetGmt ? ' by IATA' : '') . '"><i class="fa fa-clock-o' . ($offsetGmt ? ' success' : '') . '"></i> ' . Html::encode($dt->format('H:i')) . '</b>';
        }
        return '<b title="TZ (' . $dt->format("P") . ')' . (!$offsetGmt ? ' by IATA' : '') . '"><i class="fa fa-clock-o' . ($offsetGmt ? ' success' : '') . '"></i> ' . Html::encode($dt->format('H:i')) . '</b>';
    }

}
