<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask\repository;

use modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask;

/**
 * Class ShiftScheduleEventTask
 *
 * @property ShiftScheduleEventTask $model
 */
class ShiftScheduleEventTaskRepository extends \src\repositories\AbstractRepositoryWithEvent
{
    /**
     * @param ShiftScheduleEventTask $model
     */
    public function __construct(ShiftScheduleEventTask $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ShiftScheduleEventTask
    {
        return $this->model;
    }
}
