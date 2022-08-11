<?php

namespace modules\objectSegment\src\repositories;

use modules\objectSegment\src\entities\ObjectSegmentTask;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * @property ObjectSegmentTask $model
 */
class ObjectSegmentTaskRepository extends AbstractRepositoryWithEvent
{
    public function __construct(ObjectSegmentTask $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ObjectSegmentTask
    {
        return $this->model;
    }
}
