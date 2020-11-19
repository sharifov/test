<?php

namespace common\models\query;

use common\models\Department;
use common\models\Lead;
use yii\db\ActiveQuery;

/**
 * Class LeadQuery
 */
class LeadQuery extends ActiveQuery
{
    public function findLastActiveLeadByDepartmentClient(int $departmentId, int $clientId, ?int $projectId): self
    {
        return $this->findLastLeadByDepartmentClient($departmentId, $clientId, $projectId)
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]]);
    }

    public function findLastActiveSalesLeadByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastSalesLeadByClient($clientId, $projectId)
            ->andWhere(['NOT IN', 'status', [
                Lead::STATUS_SOLD, Lead::STATUS_TRASH, Lead::STATUS_REJECT
            ]]);
    }

    public function findLastSalesLeadByClient(int $clientId, ?int $projectId): self
    {
        return $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['l_dep_id' => Department::DEPARTMENT_SALES])
            ->andFilterWhere(['project_id' => $projectId])
            ->orderBy(['l_last_action_dt' => SORT_DESC])
            ->limit(1);
    }

    public function findLastLeadByDepartmentClient(int $departmentId, int $clientId, ?int $projectId): self
    {
        return $this
            ->andWhere(['client_id' => $clientId])
            ->andWhere(['l_dep_id' => $departmentId])
            ->andFilterWhere(['project_id' => $projectId])
            ->orderBy(['l_last_action_dt' => SORT_DESC])
            ->limit(1);
    }

    public function active(): self
    {
        return $this->andWhere(['status' => [
            Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING, Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
        ]]);
    }
}
