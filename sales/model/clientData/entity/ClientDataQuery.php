<?php

namespace sales\model\clientData\entity;

class ClientDataQuery
{
    public static function createOrIncrementValue(int $clientId, int $keyId, \DateTimeImmutable $dateTime): int
    {
        return ClientData::getDb()->createCommand(
            "insert into " . ClientData::tableName() . " (`cd_client_id`, `cd_key_id`, `cd_field_value`, `cd_created_dt`) values (:value1, :value2, :value3, :value4) on duplicate key update cd_field_value = cd_field_value + :value3",
            [
                ':value1' => $clientId,
                ':value2' => $keyId,
                ':value3' => 1,
                ':value4' => $dateTime->format('Y-m-d H:i:s')
            ]
        )->execute();
    }
}
