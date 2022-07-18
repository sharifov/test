<?php

namespace src\model\clientData\entity;

use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ClientDataQuery
{
    public static function createOrIncrementValue(int $clientId, int $keyId, \DateTimeImmutable $dateTime): int
    {
        if (self::existsByKeyAndClient($clientId, $keyId)) {
            return ClientData::getDb()->createCommand(
                'update ' . ClientData::tableName() . ' set `cd_field_value` = `cd_field_value` + :value where cd_client_id = :clientId and cd_key_id = :keyId',
                [
                    ':value' => 1,
                    ':clientId' => $clientId,
                    ':keyId' => $keyId
                ]
            )->execute();
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
        return self::findByClientAndKeyQuery($clientId, $keyId)->exists();
    }

    public static function findOneByClientAndKeyId(int $clientId, int $keyId): ?ClientData
    {
        return self::findByClientAndKeyQuery($clientId, $keyId)->limit(1)->one();
    }

    /**
     * @param int $clientId
     * @param int $keyId
     * @return ClientData[]
     */
    public static function findByClientAndKeyId(int $clientId, int $keyId): array
    {
        return self::findByClientAndKeyQuery($clientId, $keyId)->all();
    }

    /**
     * @param int $clientId
     * @param int $keyId
     * @return ClientDataScopes|ClientData
     */
    private static function findByClientAndKeyQuery(int $clientId, int $keyId)
    {
        return ClientData::find()->byKey($keyId)->byClientId($clientId);
    }

    public static function removeByClientAndKey(int $clientId, int $key): int
    {
        return ClientData::deleteAll(['cd_client_id' => $clientId, 'cd_key_id' => $key]);
    }
}
