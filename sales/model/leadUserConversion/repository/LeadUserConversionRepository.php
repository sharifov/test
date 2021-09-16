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
        if ($this->exist($model->luc_lead_id, $model->luc_user_id)) {
            return $model;
        }
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model));
        }
        return $model;
    }

    public function exist(int $leadId, int $userId): bool
    {
        return LeadUserConversion::find()->where(['luc_lead_id' => $leadId, 'luc_user_id' => $userId])->exists();
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
