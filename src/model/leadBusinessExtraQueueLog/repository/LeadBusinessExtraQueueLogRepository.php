<?php

namespace src\model\leadBusinessExtraQueueLog\repository;

use src\repositories\AbstractRepositoryWithEvent;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;

class LeadBusinessExtraQueueLogRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadBusinessExtraQueueLog $model
     */
    public function __construct(LeadBusinessExtraQueueLog $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadBusinessExtraQueueLog
    {
        return $this->model;
    }
}
