<?php

namespace src\model\leadStatusReason\entity;

class LeadStatusReasonQuery
{
    public static function getLeadStatusReasonByKey(string $key): ?LeadStatusReason
    {
        return LeadStatusReason::find()->byKey($key)->enabled()->one();
    }
}
