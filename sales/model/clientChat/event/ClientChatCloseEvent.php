<?php


namespace sales\model\clientChat\event;

/**
 * Class ClientChatCloseEvent
 *
 * @property int $clientChatId
 * @property bool $shallowClose
 */
class ClientChatCloseEvent
{
    public $clientChatId;
    public $shallowClose;

    /**
     * @param int $clientChatId
     * @param bool $shallowClose
     */
    public function __construct(int $clientChatId, bool $shallowClose = true)
    {
        $this->clientChatId = $clientChatId;
        $this->shallowClose = $shallowClose;
    }
}
