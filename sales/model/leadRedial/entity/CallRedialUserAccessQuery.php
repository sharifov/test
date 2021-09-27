<?php

namespace sales\model\leadRedial\entity;

class CallRedialUserAccessQuery
{
    public static function insertOrUpdate(int $leadId, int $agentId, \DateTimeImmutable $dateTimeNow): CallRedialUserAccess
    {
        $callUserAccess = CallRedialUserAccess::create($leadId, $agentId, $dateTimeNow);
        CallRedialUserAccess::getDb()->createCommand(
            "insert into " . CallRedialUserAccess::tableName() . " (`crua_lead_id`, `crua_user_id`, `crua_created_dt`) values (:value, :value2, :value3) on duplicate key update crua_created_dt = :value3, crua_lead_id = :value, crua_user_id = :value2",
            [
                ':value' => $callUserAccess->crua_lead_id,
                ':value2' => $callUserAccess->crua_user_id,
                ':value3' => $callUserAccess->crua_created_dt,
            ]
        )->execute();
        $callUserAccess->setIsNewRecord(false);
        return $callUserAccess;
    }
}
