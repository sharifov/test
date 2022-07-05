<?php

namespace src\model\leadData\entity;

/**
* Class LeadDataQuery
*/
class LeadDataQuery
{
    public static function getOneByLeadKeyValue(int $leadId, string $key, string $value): ?LeadData
    {
        return LeadData::find()
            ->where(['ld_lead_id' => $leadId])
            ->andWhere(['ld_field_key' => $key])
            ->andWhere(['ld_field_value' => $value])
            ->one();
    }


}
