<?php

namespace modules\shiftSchedule\src\helpers;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;

class UserShiftScheduleHelper
{
    /**
     * @param UserShiftSchedule[] $data
     * @return array
     */
    public static function getCalendarEventsData(array $eventList): array
    {
        $data = [];
        if ($eventList) {
            foreach ($eventList as $item) {
                $data[] = self::getDataForCalendar($item);
            }
        }
        return $data;
    }

    public static function getDataForCalendar(UserShiftSchedule $event)
    {
        $dataItem = [
            'id' => $event->uss_id,
            'title' => $event->getScheduleTypeKey(), // . '-' . $item->uss_id,
            'description' => $event->getScheduleTypeTitle() . "\r\n" . '(' . $event->uss_id . ')' . ', duration: ' .
                \Yii::$app->formatter->asDuration($event->uss_duration * 60),
            'start' => date('c', strtotime($event->uss_start_utc_dt)),
            'end' => date('c', strtotime($event->uss_end_utc_dt)),
            'color' => $event->shiftScheduleType ? $event->shiftScheduleType->sst_color : 'gray',
            'resource' => 'us-' . $event->uss_user_id,
            //'textColor' => 'black' // an option!
            'extendedProps' => [
                'icon' => $event->shiftScheduleType->sst_icon_class ?? '',
            ],
            'status' => $event->statusName,
            'username' => $event->user->username
        ];

        if (!in_array($event->uss_status_id, [UserShiftSchedule::STATUS_DONE, UserShiftSchedule::STATUS_APPROVED], true)) {
            $dataItem['borderColor'] = '#000000';
            $dataItem['title'] .= ' (' . $event->getStatusName() . ')';
            $dataItem['description'] .= ' (' . $event->getStatusName() . ')';
        }

        return $dataItem;
    }
}
