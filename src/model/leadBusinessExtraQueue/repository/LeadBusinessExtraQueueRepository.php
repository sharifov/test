<?php

namespace src\model\leadBusinessExtraQueue\repository;

use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\repositories\AbstractRepositoryWithEvent;

class LeadBusinessExtraQueueRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadBusinessExtraQueue $model
     */
    public function __construct(LeadBusinessExtraQueue $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadBusinessExtraQueue
    {
        return $this->model;
    }
}
