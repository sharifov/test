<?php

namespace sales\entities\cases;

use common\models\Department;
use yii\db\ActiveQuery;

class CasesQuery extends ActiveQuery
{
    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return $this
     */
    public function findLastActiveExchangeCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastActiveCaseByClient($clientId, $projectId)
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_EXCHANGE]);
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return $this
     */
    public function findLastActiveSupportCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastActiveCaseByClient($clientId, $projectId)
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_SUPPORT]);
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return $this
     */
    public function findLastActiveCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['NOT IN', 'cs_status', [
                CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH
            ]])
            ->andFilterWhere(['cs_project_id' => $projectId])
            ->orderBy(['cs_last_action_dt' => SORT_DESC])
            ->limit(1);
    }
}
