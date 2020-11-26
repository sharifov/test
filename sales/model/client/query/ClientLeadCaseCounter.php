<?php

namespace sales\model\client\query;

use common\models\Lead;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\db\Query;

/**
 * Class LeadCaseCounter
 *
 * @property int $clientId
 * @property int $userId
 */
class ClientLeadCaseCounter
{
    private int $clientId;
    private int $userId;

    public function __construct(int $clientId, int $userId)
    {
        $this->clientId = $clientId;
        $this->userId = $userId;
    }

    public function countActiveLeads(): int
    {
        $query = (new Query())->select(['client_id', 'status'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'status', [Lead::STATUS_TRASH, Lead::STATUS_SOLD, Lead::STATUS_REJECT]]);

        return $this->count($query, 'project_id');
    }

    public function countAllLeads(): int
    {
        $query = (new Query())->select(['client_id'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId]);

        return $this->count($query, 'project_id');
    }

    public function countActiveCases(): int
    {
        $query = (new Query())->select(['cs_client_id', 'cs_status'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]]);

        return $this->count($query, 'cs_project_id');
    }

    public function countAllCases(): int
    {
        $query = (new Query())->select(['cs_client_id'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId]);

        return $this->count($query, 'cs_project_id');
    }

    private function count(Query $q, string $projectFieldName): int
    {
        $q->addSelect([$projectFieldName])->andWhere([$projectFieldName => array_keys(EmployeeProjectAccess::getProjects($this->userId))]);

        return $q->count();
    }
}
