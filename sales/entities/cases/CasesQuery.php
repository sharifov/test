<?php

namespace sales\entities\cases;

use common\models\Department;
use yii\db\ActiveQuery;

class CasesQuery extends ActiveQuery
{
    public function findLastActiveClientCaseByDepartment(int $departmentId, int $clientId, ?int $projectId, int $trashActiveDaysLimit): self
    {
        return $this->findLastActiveClientCase($clientId, $projectId, $trashActiveDaysLimit)->byDepartment($departmentId);
    }

    public function findLastClientCaseByDepartment(int $departmentId, int $clientId, ?int $projectId): self
    {
        return $this->findLastClientCase($clientId, $projectId)->byDepartment($departmentId);
    }

    public function findLastActiveClientCase(int $clientId, ?int $projectId, int $trashActiveDaysLimit): self
    {
        $query = $this->findLastClientCase($clientId, $projectId)->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED]]);

        if ($trashActiveDaysLimit > 0) {
            $limit = (new \DateTimeImmutable())->modify('- ' . $trashActiveDaysLimit . 'day');
            $query->andWhere(['OR',
                ['NOT IN', 'cs_status', [CasesStatus::STATUS_TRASH]],
                ['>', 'cs_created_dt', $limit->format('Y-m-d H:i:s')],
            ]);
        } else {
            $query->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_TRASH]]);
        }

        return $query;
    }

    public function findLastClientCase(int $clientId, ?int $projectId): self
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

    public function byDepartment(int $departmentId): self
    {
        return $this->andWhere(['cs_dep_id' => $departmentId]);
    }

    public function withNotFinishStatus(): self
    {
        return $this->andWhere(['NOT IN', 'cs_status', [CasesStatus::STATUS_SOLVED, CasesStatus::STATUS_TRASH]]);
    }

    /**
     * @param null $db
     * @return Cases[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param null $db
     * @return Cases|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
