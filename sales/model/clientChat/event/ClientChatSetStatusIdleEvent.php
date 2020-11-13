<?php


namespace sales\model\clientChat\event;

/**
 * Class ClientChatSetStatusIdleEvent
 *
 * @property int $clientChatId
 */
class ClientChatSetStatusIdleEvent
{
    public $clientChatId;

    /**
     * @param int $clientChatId
     */
    public function __construct(int $clientChatId)
    {
        $this->clientChatId = $clientChatId;
    }
}
