<?php

namespace sales\repositories\clientChatStatusLogRepository;

use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;

class ClientChatStatusLogRepository
{
    public function getPrevious(int $chatId): ?ClientChatStatusLog
    {
        if ($log = ClientChatStatusLog::find()->andWhere(['csl_cch_id' => $chatId])->orderBy(['csl_id' => SORT_DESC])->limit(1)->one()) {
            return $log;
        }
        return null;
    }

    public function getPreviousOwnerId(int $chatId): ?int
    {
        if ($log = ClientChatStatusLog::find()->select(['csl_user_id'])->andWhere(['csl_cch_id' => $chatId])->orderBy(['csl_id' => SORT_DESC])->asArray()->one()) {
            return $log['csl_user_id'] ? (int)$log['csl_user_id'] : null;
        }
        return null;
    }

    public function save(ClientChatStatusLog $clientChatStatusLog): int
    {
        if (!$clientChatStatusLog->save(false)) {
            throw new \RuntimeException('Client Chat Status Log Saving error');
        }
        return $clientChatStatusLog->csl_id;
    }
}
