<?php

namespace src\model\leadPoorProcessingLog\repository;

use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class LeadPoorProcessingLogRepository
 *
 * @property LeadPoorProcessingLog $model
 */
class LeadPoorProcessingLogRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadPoorProcessingLog $model
     */
    public function __construct(LeadPoorProcessingLog $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadPoorProcessingLog
    {
        return $this->model;
    }
}
