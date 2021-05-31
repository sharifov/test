<?php

namespace sales\model\leadData\services;

use common\models\Lead;
use sales\model\leadData\entity\LeadData;

/**
 * Class LeadDataService
 */
class LeadDataService
{
    public static function getByLeadForApi(Lead $lead): array
    {
        $result = [];
        if (!$lead->leadData) {
            return $result;
        }
        foreach ($lead->leadData as $leadData) {
            $result[] = self::getVisibleData($leadData);
        }
        return $result;
    }

    public static function getVisibleData(LeadData $leadData): array
    {
        return [
            'ld_field_key' => $leadData->ld_field_key,
            'ld_field_value' => $leadData->ld_field_value,
        ];
    }
}
