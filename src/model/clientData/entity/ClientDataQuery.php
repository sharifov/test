<?php

namespace src\model\clientData\entity;

class ClientDataQuery
{
    public static function createOrIncrementValue(int $clientId, int $keyId, \DateTimeImmutable $dateTime): int
    {
        if (self::existsByKeyAndClient($clientId, $keyId)) {
            return ClientData::getDb()->createCommand('update ' . ClientData::tableName() . ' set `cd_field_value` = `cd_field_value` + :value', [
                ':value' => 1
            ])->execute();
        }

        return ClientData::getDb()->createCommand(
            'insert into ' . ClientData::tableName() . ' (`cd_client_id`, `cd_key_id`, `cd_field_value`, `cd_created_dt`) values (:value1, :value2, :value3, :value4)',
            [
                ':value1' => $clientId,
                ':value2' => $keyId,
                ':value3' => 1,
                ':value4' => $dateTime->format('Y-m-d H:i:s')
            ]
        )->execute();
    }

    public static function existsByKeyAndClient(int $clientId, int $keyId): bool
    {
        return self::findOneQuery($clientId, $keyId)->exists();
    }

    public static function findByClientAndKeyId(int $clientId, int $keyId): ?ClientData
    {
        return self::findOneQuery($clientId, $keyId)->limit(1)->one();
    }

    /**
     * @param int $clientId
     * @param int $keyId
     * @return ClientDataScopes|ClientData
     */
    private static function findOneQuery(int $clientId, int $keyId)
    {
        return ClientData::find()->byKey($keyId)->byClientId($clientId);
    }
}
