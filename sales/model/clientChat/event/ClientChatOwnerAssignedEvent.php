<?php

namespace sales\model\clientChat\event;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatOwnerAssignedEvent
 *
 * @property ClientChat $chat
 * @property int|null $oldOwner
 * @property int|null $newOwner
 */
class ClientChatOwnerAssignedEvent
{
    public ClientChat $chatId;
    public ?int $oldOwner;
    public ?int $newOwner;

    public function __construct(ClientChat $chat, ?int $oldOwner, ?int $newOwner)
    {
        $this->chat = $chat;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
    }
}
