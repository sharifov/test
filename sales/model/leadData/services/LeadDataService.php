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
            $result[] = $leadData->serialize();
        }
        return $result;
    }
}
