<?php

namespace sales\entities\cases;

use common\models\Department;
use yii\db\ActiveQuery;

class CasesQuery extends ActiveQuery
{
    /**
     * @param int $clientId
     * @return $this
     */
    public function findLastActiveExchangeCaseByClient(int $clientId): self
    {
        return $this->findLastActiveCaseByClient($clientId)
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_EXCHANGE]);
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function findLastActiveSupportCaseByClient(int $clientId): self
    {
        return $this->findLastActiveCaseByClient($clientId)
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_SUPPORT]);
    }

    /**
     * @param int $clientId
     * @return $this
     */
    public function findLastActiveCaseByClient(int $clientId): self
    {
        return $this
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['NOT IN', 'cs_status', [
                CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH
            ]])
            ->orderBy(['cs_last_action_dt' => SORT_DESC])
            ->limit(1);
    }
}
