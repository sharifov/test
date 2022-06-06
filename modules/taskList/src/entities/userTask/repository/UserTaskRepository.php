<?php

namespace modules\taskList\src\entities\userTask\repository;

use modules\taskList\src\entities\userTask\UserTask;
use src\repositories\AbstractRepositoryYearMonthPartition;

/**
 * Class UserTaskRepository
 *
 * @property UserTask $model
 */
class UserTaskRepository extends AbstractRepositoryYearMonthPartition
{
    /**
     * @param UserTask $model
     */
    public function __construct(UserTask $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserTask
    {
        return $this->model;
    }
}
