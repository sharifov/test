<?php

namespace sales\model\leadUserConversion\repository;

use sales\helpers\ErrorsToStringHelper;
use sales\model\leadUserConversion\entity\LeadUserConversion;

/**
 * Class LeadUserConversionRepository
 */
class LeadUserConversionRepository
{
    public function save(LeadUserConversion $model): LeadUserConversion
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }

    /**
     * @param LeadUserConversion $model
     * @return bool|false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(LeadUserConversion $model)
    {
        return $model->delete();
    }
}
