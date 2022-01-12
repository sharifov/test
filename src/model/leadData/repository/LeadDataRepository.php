<?php

namespace src\model\leadData\repository;

use src\helpers\ErrorsToStringHelper;
use src\model\leadData\entity\LeadData;

/**
 * Class LeadDataRepository
 */
class LeadDataRepository
{
    public function save(LeadData $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model->ld_id;
    }
}
