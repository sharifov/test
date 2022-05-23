<?php

namespace modules\shiftSchedule\src\entities\userShiftScheduleLog;

class UserShiftScheduleLogRepository
{
    public function save(UserShiftScheduleLog $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException($model->getErrorSummary(true)[0]);
        }
        return $model->ussl_id;
    }
}
