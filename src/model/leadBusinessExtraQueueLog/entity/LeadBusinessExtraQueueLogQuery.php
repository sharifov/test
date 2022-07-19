<?php

namespace src\model\leadBusinessExtraQueueLog\entity;

class LeadBusinessExtraQueueLogQuery
{
    public static function getLastLeadBusinessExtraQueueLog(int $leadId): ?LeadBusinessExtraQueueLog
    {
        return LeadBusinessExtraQueueLog
            ::find()
            ->where(['lbeql_lead_id' => $leadId])
            ->orderBy(['lbeql_id' => SORT_DESC])
            ->one();
    }
}
