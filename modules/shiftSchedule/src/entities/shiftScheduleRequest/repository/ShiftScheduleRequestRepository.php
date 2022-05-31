<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRequest\repository;

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * @property ShiftScheduleRequest $model
 */
class ShiftScheduleRequestRepository extends AbstractRepositoryWithEvent
{
    public function __construct(ShiftScheduleRequest $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ShiftScheduleRequest
    {
        return $this->model;
    }
}
