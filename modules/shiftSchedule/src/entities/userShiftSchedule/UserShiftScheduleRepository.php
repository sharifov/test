<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

class UserShiftScheduleRepository
{
    public function save(UserShiftSchedule $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException('User Shift Schedule saving failed');
        }
        return $model->uss_id;
    }
}
