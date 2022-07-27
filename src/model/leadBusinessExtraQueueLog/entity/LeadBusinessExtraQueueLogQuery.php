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

    public static function isLeadWasInBusinessExtraQueue(int $leadId): bool
    {
        return LeadBusinessExtraQueueLog
            ::find()
            ->where(['lbeql_lead_id' => $leadId])
            ->andWhere(['lbeql_status' => LeadBusinessExtraQueueLogStatus::STATUS_ADDED_TO_BUSINESS_EXTRA_QUEUE])
            ->exists();
    }
}
