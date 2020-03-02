<?php

namespace sales\entities\cases;

use common\models\Department;
use yii\db\ActiveQuery;

class CasesQuery extends ActiveQuery
{
    public function findLastActiveExchangeCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastActiveCaseByClient($clientId, $projectId)->byExchange();
    }

    public function findLastExchangeCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastCaseByClient($clientId, $projectId)->byExchange();
    }

    public function findLastActiveSupportCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastActiveCaseByClient($clientId, $projectId)->bySupport();
    }

    public function findLastSupportCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastCaseByClient($clientId, $projectId)->bySupport();
    }

    /**
     * @param int $clientId
     * @param int|null $projectId
     * @return $this
     */
    public function findLastActiveCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this->findLastCaseByClient($clientId, $projectId)
            ->andWhere(['NOT IN', 'cs_status', [
                CasesStatus::STATUS_SOLVED
            ]]);
    }

    public function findLastCaseByClient(int $clientId, ?int $projectId): self
    {
        return $this
            ->andWhere(['cs_client_id' => $clientId])
            ->andFilterWhere(['cs_project_id' => $projectId])
            ->orderBy(['cs_last_action_dt' => SORT_DESC])
            ->limit(1);
    }

    public function bySupport(): self
    {
        return $this->andWhere(['cs_dep_id' => Department::DEPARTMENT_SUPPORT]);
    }

    public function byExchange(): self
    {
        return $this->andWhere(['cs_dep_id' => Department::DEPARTMENT_EXCHANGE]);
    }
}
