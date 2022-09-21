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

    public static function countSnoozeLeadsByOwner(int $userId): int
    {
        return Lead::find()->where(['status' => Lead::STATUS_SNOOZE, 'employee_id' => $userId])->count();
    }

    public function byClient(int $clientId): self
    {
        return $this->andWhere(['client_id' => $clientId]);
    }

    public function sold(): self
    {
        return $this->andWhere(['status' => Lead::STATUS_SOLD]);
    }

    public function byId(int $id): self
    {
        return $this->andWhere(['id' => $id]);
    }

    public function byBookingId(string $bookingId): self
    {
        return $this->andWhere(['hybrid_uid' => $bookingId]);
    }

    /**
     * @param null $db
     * @return array|Lead[]
     */
    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }

    /**
     * @param null $db
     * @return array|Lead|null
     */
    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }

    public static function getLeadById(int $leadId): ?Lead
    {
        return Lead::find()->byId($leadId)->limit(1)->one();
    }

    /**
     * @param string $bookingId
     * @return Lead|null
     */
    public static function getLeadByBookingId(string $bookingId): ?Lead
    {
        return Lead::find()->byBookingId($bookingId)->limit(1)->one();
    }
}
