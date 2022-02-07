<?php

namespace src\model\leadPoorProcessingLog\entity;

/**
 * Class LeadPoorProcessingLogQuery
 */
class LeadPoorProcessingLogQuery
{
    public static function getLastLeadPoorProcessingLog(int $leadId): ?LeadPoorProcessingLog
    {
        return LeadPoorProcessingLog::find()
            ->where(['lppl_lead_id' => $leadId])
            ->orderBy(['lppl_id' => SORT_DESC])
            ->one()
        ;
    }
}
