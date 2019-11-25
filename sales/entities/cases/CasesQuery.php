<?php

namespace sales\entities\cases;

use common\models\Department;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

class CasesQuery extends ActiveQuery
{
    /**
     * @param int $clientId
     * @return Cases|null
     */
    public function findLastActiveExchangeCaseByClient(int $clientId):? Cases
    {
        return $this
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_EXCHANGE])
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['NOT IN', 'cs_status', [
                CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH
            ]])
            ->orderBy(['cs_updated_dt' => SORT_DESC])
            ->one();
    }

    /**
     * @param int $clientId
     * @return Cases|null
     */
    public function findLastActiveSupportCaseByClient(int $clientId):? Cases
    {
        return $this
            ->andWhere(['cs_dep_id' => Department::DEPARTMENT_SUPPORT])
            ->andWhere(['cs_client_id' => $clientId])
            ->andWhere(['NOT IN', 'cs_status', [
                CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH
            ]])
            ->orderBy(['cs_updated_dt' => SORT_DESC])
            ->one();
    }
}
