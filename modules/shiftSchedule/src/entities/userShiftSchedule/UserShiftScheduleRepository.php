<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

use src\repositories\AbstractRepositoryWithEvent;

class UserShiftScheduleRepository extends AbstractRepositoryWithEvent
{
    public function __construct(UserShiftSchedule $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserShiftSchedule
    {
        return $this->model;
    }
}
