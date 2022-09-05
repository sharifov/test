<?php

namespace modules\objectTask\src\entities\repositories;

use modules\objectTask\src\entities\ObjectTaskStatusLog;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * @property ObjectTaskStatusLog $model
 */
class ObjectTaskStatusLogRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param ObjectTaskStatusLog $model
     */
    public function __construct(ObjectTaskStatusLog $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ObjectTaskStatusLog
    {
        return $this->model;
    }
}
