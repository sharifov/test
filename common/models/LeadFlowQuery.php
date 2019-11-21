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
        $descriptions = array_merge($default, $flowDescriptions);

        $query =  $this->andWhere([
//            'employee_id' => $userId,
            'lf_owner_id' => $userId,
            'status' => Lead::STATUS_PROCESSING,
            'lf_description' => $descriptions,
        ]);

        if (!in_array(LeadFlow::DESCRIPTION_MANUAL_CREATE, $descriptions, false)) {
            $query->andWhere(['lf_from_status_id' => Lead::STATUS_PENDING]);
        }

        $query->asArray()->orderBy(['created' => SORT_DESC])->limit(1);

        return $query;
    }

    /**
     * @param int $leadId
     * @return LeadFlow|null
     */
    public function last(int $leadId):? LeadFlow
    {
        return $this->andWhere(['lead_id' => $leadId])->orderBy(['created' => SORT_DESC])->limit(1)->one();
    }
}
