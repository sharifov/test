<?php

namespace modules\lead\src\entities\lead;

use common\models\Lead;
use common\models\LeadFlow;

class LeadQuery
{
    public static function getLastActiveUserId(int $leadId): ?int
    {
        if (
            $query = LeadFlow::find()
            ->select(['lf_owner_id'])
            ->andWhere(['lead_id' => $leadId])
            ->andWhere(['IS NOT', 'lf_owner_id', null])
            ->asArray()
            ->orderBy(['id' => SORT_DESC])->limit(1)->one()
        ) {
            return $query['lf_owner_id'];
        }
        return null;
    }

    public static function countSoldLeadsByClient(int $clientId): int
    {
        return (int)Lead::find()->sold()->byClient($clientId)->count();
    }
}
