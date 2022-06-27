<?php

namespace src\model\leadData\services;

use common\models\Lead;
use src\model\leadData\entity\LeadData;

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

    public static function getByLeadFields(
        Lead $lead,
        array $fields = ['key' => 'ld_field_key', 'value' => 'ld_field_value']
    ): array {
        $result = [];
        if (!$lead->leadData) {
            return $result;
        }
        foreach ($lead->leadData as $leadData) {
            $result[] = $leadData->setFields($fields)->toArray();
        }
        return $result;
    }

    /**
     * @param int $leadId
     * @param string $key
     * @return LeadData[]
     */
    public static function getByKey(int $leadId, string $key): array
    {
        return LeadData::find()
            ->where(['ld_lead_id' => $leadId])
            ->andWhere(['ld_field_key' => $key])
            ->all();
    }
}
