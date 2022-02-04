<?php

namespace src\model\leadPoorProcessingData\repository;

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class LeadPoorProcessingDataRepository
 *
 * @property LeadPoorProcessingData $model
 */
class LeadPoorProcessingDataRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadPoorProcessingData $model
     */
    public function __construct(LeadPoorProcessingData $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadPoorProcessingData
    {
        return $this->model;
    }
}
