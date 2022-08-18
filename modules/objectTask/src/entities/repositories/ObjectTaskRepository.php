<?php

namespace modules\objectTask\src\entities\repositories;

use modules\objectTask\src\entities\ObjectTask;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * @property ObjectTask $model
 */
class ObjectTaskRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param ObjectTask $model
     */
    public function __construct(ObjectTask $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ObjectTask
    {
        return $this->model;
    }
}
