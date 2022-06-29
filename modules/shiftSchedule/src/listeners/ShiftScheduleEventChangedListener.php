<?php

namespace modules\shiftSchedule\src\listeners;

use modules\shiftSchedule\src\events\ShiftScheduleEventChangedEvent;
use modules\shiftSchedule\src\services\ShiftScheduleRequestService;
use src\helpers\app\AppHelper;
use Yii;
use yii\helpers\ArrayHelper;

class ShiftScheduleEventChangedListener
{
    /**
     * @param ShiftScheduleEventChangedEvent $event
     * @return void
     */
    public function handle(ShiftScheduleEventChangedEvent $event): void
    {
        try {
            $requestService = \Yii::createObject(ShiftScheduleRequestService::class);
            $requestService->changeDueToEventChange($event->event, $event->oldEvent, $event->changedAttributes, $event->user);
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['UserShiftScheduleID' => $event->event->uss_id]);
            Yii::warning($message, 'ShiftScheduleEventChangedListener::handle::Throwable');
        }
    }
}
