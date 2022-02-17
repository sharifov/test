<?php

namespace src\model\leadPoorProcessingData\repository;

use src\model\leadUserData\entity\LeadUserData;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class LeadUserDataRepository
 *
 * @property LeadUserData $model
 */
class LeadUserDataRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param LeadUserData $model
     */
    public function __construct(LeadUserData $model)
    {
        parent::__construct($model);
    }

    public function getModel(): LeadUserData
    {
        return $this->model;
    }
}
