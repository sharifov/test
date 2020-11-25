<?php

namespace sales\model\clientChatMessage\event;

use sales\model\clientChatMessage\entity\ClientChatMessage;

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
