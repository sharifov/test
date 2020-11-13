<?php


namespace sales\model\clientChat\event;

/**
 * Class ClientChatCloseEvent
 *
 * @property int $clientChatId
 * @property bool $shallowClose
 * @property int $chatOwnerId
 */
class ClientChatCloseEvent
{
    public $clientChatId;
    public $shallowClose;
    public $chatOwnerId;

    /**
     * @param int $clientChatId
     * @param bool $shallowClose
     * @param int $chatOwnerId
     */
    public function __construct(int $clientChatId, bool $shallowClose = true, int $chatOwnerId = 0)
    {
        $this->clientChatId = $clientChatId;
        $this->shallowClose = $shallowClose;
        $this->chatOwnerId = $chatOwnerId;
    }
}
