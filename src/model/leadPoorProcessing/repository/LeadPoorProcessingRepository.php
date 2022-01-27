<?php

namespace src\model\leadPoorProcessing\repository;

use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class LeadPoorProcessingRepository
 *
 * @property LeadPoorProcessing $model
 */
class LeadPoorProcessingRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadPoorProcessing $model
     */
    public function __construct(LeadPoorProcessing $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadPoorProcessing
    {
        return $this->model;
    }
}
