<?php

namespace sales\model\leadData\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\leadData\entity\LeadData;

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
