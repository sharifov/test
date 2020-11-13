<?php


namespace sales\model\clientChat\event;

/**
 * Class ClientChatCloseEvent
 *
 * @property int $clientChatId
 */
class ClientChatSetStatusCloseEvent
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
