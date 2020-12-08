<?php

namespace sales\model\clientChat\entity;

use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;

class ClientChatQuery
{
    public static function lastSameChat(string $rid): ?ClientChat
    {
        return ClientChat::find()->byRid($rid)->last()->one();
    }

    public static function lastSameChatId(string $rid): ?int
    {
        if ($chat = ClientChat::find()->select(['cch_id'])->byRid($rid)->last()->asArray()->one()) {
            return (int)$chat['cch_id'];
        }
        return null;
    }

    /**
     * Timeout is int in hours
     *
     * @param int $timeoutHours
     * @return ClientChat[]
     */
    public static function getLastUpdatedClosedChatsIdsByTimeout(int $timeoutHours): array
    {
        return ClientChat::find()
            ->leftJoin(ClientChatStatusLog::tableName(), 'cch_id = csl_cch_id')
            ->andWhere(['>=', 'timestampdiff(HOUR, csl_start_dt, :dt)', $timeoutHours], ['dt' => date('Y-m-d H:i:s')])
            ->andWhere(['csl_end_dt' => null])
            ->byStatus(ClientChat::STATUS_CLOSED)
            ->groupBy(['cch_id'])
            ->all();
    }

    public static function isExistsNotClosedArchivedChatByRid(string $rid): bool
    {
        return ClientChat::find()->byRid($rid)->notInClosedGroup()->exists();
    }

    public static function isChildExistByChatId(int $cchId): bool
    {
        return ClientChat::find()->byParent($cchId)->exists();
    }
}
