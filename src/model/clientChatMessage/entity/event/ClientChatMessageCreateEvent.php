<?php

namespace src\model\clientChatMessage\event;

use src\model\clientChatMessage\entity\ClientChatMessage;

/**
 * Class ClientChatMessageCreateEvent
 *
 * @property ClientChatMessage $clientChatMessage
 */
class ClientChatMessageCreateEvent
{
    public $clientChatMessage;

    /**
     * @param ClientChatMessage $clientChatMessage
     */
    public function __construct(ClientChatMessage $clientChatMessage)
    {
        $this->clientChatMessage = $clientChatMessage;
    }
}
