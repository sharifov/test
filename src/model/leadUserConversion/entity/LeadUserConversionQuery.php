<?php

namespace src\model\leadUserConversion\entity;

class LeadUserConversionQuery
{
    public static function removeByLeadId(int $id): int
    {
        return LeadUserConversion::deleteAll(['luc_lead_id' => $id]);
    }
}
