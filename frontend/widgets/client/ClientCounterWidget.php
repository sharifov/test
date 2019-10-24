<?php

namespace frontend\widgets\client;

use common\models\Lead;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\base\Widget;
use yii\db\Query;

/**
 * Class ClientCounterWidget
 *
 * @property int $clientId
 * @property int $userId
 */
class ClientCounterWidget extends Widget
{
    public $clientId;
    public $userId;

    /**
     * @return string|null
     */
    public function run(): ?string
    {
        if (!$this->clientId) {
            return null;
        }

        return $this->render('client_counter', [
            'allLeads' => $this->countAllLeads(),
            'activeLeads' => $this->countActiveLeads(),
            'allCases' => $this->countAllCases(),
            'activeCases' => $this->countActiveCases()
        ]);
    }

    /**
     * @return int
     */
    private function countActiveLeads(): int
    {
        $query = (new Query)->select(['client_id', 'status'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'status', [Lead::STATUS_TRASH, Lead::STATUS_SOLD, Lead::STATUS_REJECT]]);

        return $this->count($query, 'project_id');
    }

    /**
     * @return int
     */
    private function countAllLeads(): int
    {
        $query = (new Query)->select(['client_id'])->from(Lead::tableName())
            ->andWhere(['client_id' => $this->clientId]);

        return $this->count($query, 'project_id');
    }

    /**
     * @return int
     */
    private function countActiveCases(): int
    {
        $query = (new Query)->select(['cs_client_id', 'cs_status'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId])
            ->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]]);

        return $this->count($query, 'cs_project_id');
    }

    /**
     * @return int
     */
    private function countAllCases(): int
    {
        $query = (new Query)->select(['cs_client_id'])->from(Cases::tableName())
            ->andWhere(['cs_client_id' => $this->clientId]);

        return $this->count($query, 'cs_project_id');
    }

    /**
     * @param Query $q
     * @param string $projectFieldName
     * @return int
     */
    private function count(Query $q, string $projectFieldName): int
    {
        $q->addSelect([$projectFieldName])->andWhere([$projectFieldName => array_keys(EmployeeProjectAccess::getProjects($this->userId))]);

        return $q->count();
    }

}
