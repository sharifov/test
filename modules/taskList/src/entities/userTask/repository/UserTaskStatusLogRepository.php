<?php

namespace modules\taskList\src\entities\userTask\repository;

use modules\taskList\src\entities\userTask\UserTaskStatusLog;
use src\repositories\AbstractBaseRepository;

/**
 * Class UserTaskStatusLogRepository
 *
 * @property UserTaskStatusLog $model
 */
class UserTaskStatusLogRepository extends AbstractBaseRepository
{
    /**
     * @param UserTaskStatusLog $model
     */
    public function __construct(UserTaskStatusLog $model)
    {
        parent::__construct($model);
    }

    public function getModel(): UserTaskStatusLog
    {
        return $this->model;
    }
}
