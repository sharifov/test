<?php

namespace common\models\query;

use common\models\Lead;
use common\models\LeadFlow;
use yii\db\ActiveQuery;

class LeadFlowQuery extends ActiveQuery
{

    /**
     * @param $userId
     * @param array $flowDescriptions ['Manual create', 'Call AutoCreated Lead']
     * @param array $fromStatuses [Lead::STATUS_BOOK_FAILED, Lead::STATUS_PENDING]
     * @return $this
     */
    public function lastTakenByUserId($userId, array $flowDescriptions = [], array $fromStatuses = []): self
    {
        $default = [LeadFlow::DESCRIPTION_TAKE];
        $descriptions = array_merge($default, $flowDescriptions);

        $query =  $this->andWhere([
//            'employee_id' => $userId,
            'lf_owner_id' => $userId,
            'status' => Lead::STATUS_PROCESSING,
            'lf_description' => $descriptions,
        ]);

        if ($fromStatuses) {
            $query->andWhere(['lf_from_status_id' => $fromStatuses]);
        } else {
            if (!in_array(LeadFlow::DESCRIPTION_MANUAL_CREATE, $descriptions, false)) {
                $query->andWhere(['lf_from_status_id' => Lead::STATUS_PENDING]);
            }
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
