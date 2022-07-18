<?php

namespace src\model\leadBusinessExtraQueue\entity;

use src\model\leadPoorProcessing\entity\LeadPoorProcessing;

class LeadBusinessExtraQueueQuery
{
    public static function getByLeadAndKey(int $leadId, int $ruleId): ?LeadBusinessExtraQueue
    {
        return LeadBusinessExtraQueue
            ::find()
            ->where(['lbeq_lead_id' => $leadId])
            ->andWhere(['lbeq_lbeqr_id' => $ruleId])
            ->limit(1)
            ->one();
    }

    /**
     * @param int $leadId
     * @return LeadBusinessExtraQueue[]
     */
    public static function getAllByLeadId(int $leadId): array
    {
        return LeadBusinessExtraQueue
            ::find()
            ->where(['lbeq_lead_id' => $leadId])
            ->all();
    }
}
