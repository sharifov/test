<?php

namespace sales\model\authClient\entity;

class AuthClientQuery
{
    public static function findByUserAndSource(int $userId, int $source, string $sourceId): ?AuthClient
    {
        return AuthClient::find()->where(['ac_user_id' => $userId, 'ac_source' => $source, 'ac_source_id' => $sourceId])->one();
    }

    /**
     * @param int $source
     * @param string $sourceId
     * @return AuthClient[]|null
     */
    public static function findAllBySourceData(int $source, string $sourceId): ?array
    {
        return AuthClient::find()->with('user')->where(['ac_source' => $source, 'ac_source_id' => $sourceId])->all();
    }

    /**
     * @param int $userid
     * @return array|AuthClient[]
     */
    public static function findAllByUserId(int $userid): array
    {
        return AuthClient::find()->byUserid($userid)->all();
    }
}
