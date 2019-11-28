<?php

namespace common\models\query;

use common\models\Lead;
use yii\db\ActiveQuery;

/**
 * Class LeadQuery
 */
class LeadQuery extends ActiveQuery
{

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return Lead|null
     */
    public function findLastActiveLeadByClient(int $clientId, ?int $projectId): ?Lead
    {
        return $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]])
            ->andFilterWhere(['project_id' => $projectId])
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
