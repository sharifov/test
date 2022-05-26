<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

class UserShiftScheduleRepository
{
    public function save(UserShiftSchedule $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException($model->getErrorSummary(false)[0]);
        }
        return $model->uss_id;
    }
}
