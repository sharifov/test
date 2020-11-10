<?php

namespace sales\model\clientChatUserAccess\event;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class UpdateChatUserAccessWidgetEvent
 * @package sales\model\clientChatUserAccess\event
 *
 * @property ClientChat $chat
 * @property int $userId
 * @property int $statusId
 * @property int|null $ccuaId
 */
class UpdateChatUserAccessWidgetEvent
{
    public $chat;

    public $userId;

    public $statusId;

    public $ccuaId;

    public function __construct(ClientChat $chat, int $userId, int $statusId, ?int $ccuaId = null)
    {
        $this->chat = $chat;
        $this->userId = $userId;
        $this->statusId = $statusId;
        $this->ccuaId = $ccuaId;
    }
}
