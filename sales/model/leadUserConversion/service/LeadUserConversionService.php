<?php

namespace sales\model\leadUserConversion\service;

use sales\model\leadUserConversion\entity\LeadUserConversion;

/**
 * Class LeadUserConversionService
 */
class LeadUserConversionService
{
    public static function getUserIdsByLead(int $leadId): array
    {
        return LeadUserConversion::find()
            ->select(['luc_user_id'])
            ->where(['luc_lead_id' => $leadId])
            ->indexBy('luc_user_id')
            ->column();
    }
}
