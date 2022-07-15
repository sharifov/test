<?php

namespace common\models\query;

use common\models\Lead;
use common\models\LeadFlow;
use yii\db\ActiveQuery;

class LeadFlowQuery extends ActiveQuery
{
    /**
     * @param $userId
     * @param array $flowDescriptions ['Manual create', 'Call AutoCreated Lead']
     * @param array $fromStatuses [Lead::STATUS_BOOK_FAILED, Lead::STATUS_PENDING]
     * @return $this
     */
    public function lastTakenByUserId($userId, array $flowDescriptions = [], array $fromStatuses = []): self
    {
        $default = [LeadFlow::DESCRIPTION_TAKE];
        $descriptions = array_merge($default, $flowDescriptions);

        $query =  $this->andWhere([
//            'employee_id' => $userId,
            'lf_owner_id' => $userId,
            'status' => Lead::STATUS_PROCESSING,
            'lf_description' => $descriptions,
        ]);

        if ($fromStatuses) {
            $query->andWhere(['lf_from_status_id' => $fromStatuses]);
        } else {
            if (!in_array(LeadFlow::DESCRIPTION_MANUAL_CREATE, $descriptions, false)) {
                $query->andWhere(['lf_from_status_id' => Lead::STATUS_PENDING]);
            }
        }

        $query->asArray()->orderBy(['id' => SORT_DESC])->limit(1);

        return $query;
    }

    /**
     * @param int $leadId
     * @return LeadFlow|null
     */
    public function last(int $leadId): ?LeadFlow
    {
        return $this->andWhere(['lead_id' => $leadId])->orderBy(['created' => SORT_DESC])->limit(1)->one();
    }

    /**
     * @param null $db
     * @return LeadFlow[]|array
     */
    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }

    /**
     * @param null $db
     * @return LeadFlow|array|null
     */
    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }

    public static function countByStatus(int $leadId, int $status): int
    {
        return LeadFlow::find()->byLeadId($leadId)->byStatusId($status)->count() ?? 0;
    }

    public function byLeadId(int $id): self
    {
        return $this->andWhere(['lead_id' => $id]);
    }

    public function byStatusId(int $id): self
    {
        return $this->andWhere(['status' => $id]);
    }

    public function notEmptyOwner(): self
    {
        return $this->andWhere(['IS NOT', 'lf_owner_id', null]);
    }

    public static function getFirstOwnerOfLead(int $leadId): ?LeadFlow
    {
        return LeadFlow::find()->byLeadId($leadId)->notEmptyOwner()->orderBy(['id' => SORT_ASC])->one();
    }

    /**
     * @param int $clientId
     * @return LeadFlow[]
     */
    public static function findSoldByClient(int $clientId): array
    {
        $subQuery = Lead::find()->select(['id'])->where(['client_id' => $clientId, 'status' => Lead::STATUS_SOLD]);

        return LeadFlow::find()
            ->where(['IN', 'lead_id', $subQuery])
            ->andWhere(['status' => Lead::STATUS_SOLD])
            ->all();
    }
}
