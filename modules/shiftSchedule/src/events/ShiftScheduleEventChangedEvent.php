<?php

namespace modules\shiftSchedule\src\events;

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;

class ShiftScheduleEventChangedEvent
{
    public UserShiftSchedule $event;
    public UserShiftSchedule $oldEvent;
    public int $userId;
    public array $changedAttributes = [];

    /**
     * @param UserShiftSchedule $event
     * @param UserShiftSchedule $oldEvent
     * @param array $changedAttributes
     */
    public function __construct(UserShiftSchedule $event, UserShiftSchedule $oldEvent, array $changedAttributes, int $userId)
    {
        $this->event = $event;
        $this->oldEvent = $oldEvent;
        $this->changedAttributes = $changedAttributes;
        $this->userId = $userId;
    }
}
