<?php

namespace common\models;

use yii\db\ActiveQuery;

class LeadFlowQuery extends ActiveQuery
{

    /**
     * @param $userId
     * @return $this
     */
    public function lastTakenByUserId($userId): self
    {
        return $this->andWhere([
            'employee_id' => $userId,
            'lf_owner_id' => $userId,
            'lf_from_status_id' => Lead::STATUS_PENDING,
            'status' => Lead::STATUS_PROCESSING,
        ])->asArray()->orderBy(['created' => SORT_DESC])->limit(1);
    }
}
