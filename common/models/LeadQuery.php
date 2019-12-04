<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

/**
 * Class LeadQuery
 */
class LeadQuery extends ActiveQuery
{

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return $this
     */
    public function findLastActiveSalesLeadByClient(int $clientId, ?int $projectId): self
    {
        $query = $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['l_dep_id' => Department::DEPARTMENT_SALES])
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]])
            ->andFilterWhere(['project_id' => $projectId])
            ->orderBy(['l_last_action_dt' => SORT_DESC]);
        \Yii::error(VarDumper::dumpAsString($query->createCommand()->getRawSql()));
        return $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['l_dep_id' => Department::DEPARTMENT_SALES])
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]])
            ->andFilterWhere(['project_id' => $projectId])
            ->orderBy(['l_last_action_dt' => SORT_DESC]);
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
