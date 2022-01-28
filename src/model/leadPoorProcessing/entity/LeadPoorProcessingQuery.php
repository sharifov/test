<?php

namespace src\model\leadPoorProcessing\entity;

/**
 * Class LeadPoorProcessingQuery
 */
class LeadPoorProcessingQuery
{
    public static function getByLeadAndKey(int $leadId, int $ruleId): ?LeadPoorProcessing
    {
        return LeadPoorProcessing::find()
            ->where(['lpp_lead_id' => $leadId])
            ->andWhere(['lpp_lppd_id' => $ruleId])
            ->limit(1)
            ->one();
    }

    /**
     * @param int $leadId
     * @return LeadPoorProcessing[]
     */
    public static function getAllByLeadId(int $leadId): array
    {
        return LeadPoorProcessing::find()
            ->where(['lpp_lead_id' => $leadId])
            ->all();
    }
}
