<?php

namespace src\model\clientChatHold;

use src\model\clientChatHold\entity\ClientChatHold;

/**
 * Class ClientChatHoldRepository
 */
class ClientChatHoldRepository
{
    public function save(ClientChatHold $clientChatHold): ClientChatHold
    {
        if (!$clientChatHold->save(false)) {
            throw new \RuntimeException('Client Chat Hold saving failed');
        }
        return $clientChatHold;
    }
}
