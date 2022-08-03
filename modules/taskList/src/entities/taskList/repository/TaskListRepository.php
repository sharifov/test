<?php

namespace modules\taskList\src\entities\taskList\repository;

use modules\taskList\src\entities\taskList\TaskList;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class LeadPoorProcessingDataRepository
 *
 * @property TaskList $model
 */
class TaskListRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param TaskList $model
     */
    public function __construct(TaskList $model)
    {
        parent::__construct($model);
    }

    public function getModel(): TaskList
    {
        return $this->model;
    }
}
