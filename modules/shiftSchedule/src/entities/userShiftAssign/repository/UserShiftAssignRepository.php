<?php

namespace modules\shiftSchedule\src\entities\userShiftAssign\repository;

use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class UserShiftAssignRepository
 *
 * @property UserShiftAssign $model
 */
class UserShiftAssignRepository extends AbstractRepositoryWithEvent
{
    public function __construct(UserShiftAssign $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserShiftAssign
    {
        return $this->model;
    }
}
