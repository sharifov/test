<?php

namespace sales\model\client\query;

use sales\model\clientChat\entity\ClientChat;
use yii\db\Query;

/**
 * Class LeadCaseCounter
 *
 * @property int $clientId
 */
class ClientChatCounter
{
    private int $clientId;

    public function __construct(int $clientId)
    {
        $this->clientId = $clientId;
    }

    public function countActiveChats(): int
    {
        $query = ClientChat::find()
            ->select(['cch_client_id', 'cch_status_id'])
            ->byClientId($this->clientId)
            ->notInClosedGroup();

        return $this->count($query);
    }

    public function countAllChats(): int
    {
        $query = ClientChat::find()
            ->select(['cch_client_id'])
            ->byClientId($this->clientId);

        return $this->count($query);
    }

    private function count(Query $q): int
    {
        return $q->count();
    }
}
