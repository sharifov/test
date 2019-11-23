<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * Class LeadQuery
 */
class LeadQuery extends ActiveQuery
{

    /**
     * @param int $clientId
     * @return Lead|null
     */
    public function findLastActiveLeadByClient(int $clientId):? Lead
    {
        return $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]])
            ->orderBy(['l_last_action_dt' => SORT_DESC])
            ->one();
    }

    /**
     * @return $this
     */
    public function active(): self
    {
        return $this->andWhere(['status' => [
            Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
        ]]);
    }

}
