<?php

namespace common\models;

use yii\db\ActiveQuery;

class LeadFlowQuery extends ActiveQuery
{

    /**
     * @param $userId
     * @param array $flowDescriptions ['Manual create', 'Call AutoCreated Lead']
     * @return $this
     */
    public function lastTakenByUserId($userId, array $flowDescriptions = []): self
    {
        $default = [LeadFlow::DESCRIPTION_TAKE];
        $description = array_merge($default, $flowDescriptions);
        return $this->andWhere([
//            'employee_id' => $userId,
            'lf_owner_id' => $userId,
//            'lf_from_status_id' => Lead::STATUS_PENDING,
            'status' => Lead::STATUS_PROCESSING,
            ])->andWhere(['lf_description' => $description])
            ->asArray()->orderBy(['created' => SORT_DESC])->limit(1);
    }
}
