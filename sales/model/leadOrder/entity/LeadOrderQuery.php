<?php

namespace sales\model\leadOrder\entity;

use common\models\Lead;

class LeadOrderQuery
{
    public static function findLeadFailedBookByOrderIdProjectId(int $orderId, int $projectId): ?LeadOrder
    {
        $query = LeadOrder::find();

        return $query->where(['lo_order_id' => $orderId])
            ->innerJoin(
                Lead::tableName(),
                'id = lo_lead_id and status = :statusId and project_id = :projectId',
                [
                    'statusId' => Lead::STATUS_BOOK_FAILED,
                    'projectId' => $projectId
                ]
            )
            ->one();
    }
}
