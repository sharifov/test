<?php

namespace sales\model\userAuthClient\entity;

class UserAuthClientQuery
{
    public static function findByUserAndSource(int $userId, int $source, string $sourceId): ?UserAuthClient
    {
        return UserAuthClient::find()->where(['uac_user_id' => $userId, 'uac_source' => $source, 'uac_source_id' => $sourceId])->one();
    }

    /**
     * @param int $source
     * @param string $sourceId
     * @return UserAuthClient[]|null
     */
    public static function findAllBySourceData(int $source, string $sourceId): ?array
    {
        return UserAuthClient::find()->with('user')->where(['uac_source' => $source, 'uac_source_id' => $sourceId])->all();
    }

    /**
     * @param int $userid
     * @return array|UserAuthClient[]
     */
    public static function findAllByUserId(int $userid): array
    {
        return UserAuthClient::find()->byUserid($userid)->all();
    }
}
