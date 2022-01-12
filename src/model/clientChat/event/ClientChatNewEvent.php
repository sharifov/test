<?php

namespace src\model\clientChat\event;

use src\model\clientChat\entity\ClientChat;

class ClientChatNewEvent
{
    public $chat;
    public $oldStatus;
    public $ownerId;
    public $creatorUserId;
    public $description;
    public $prevChannelId;
    public $actionType;
    public $reasonId;
    public $rid;

    public function __construct(
        ClientChat $chat,
        ?int $oldStatus,
        ?int $ownerId,
        ?int $creatorUserId,
        ?string $description,
        ?int $prevChannelId,
        int $actionType,
        ?int $reasonId,
        ?string $rid
    ) {
        $this->chat = $chat;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->creatorUserId = $creatorUserId;
        $this->description = $description;
        $this->prevChannelId = $prevChannelId;
        $this->actionType = $actionType;
        $this->reasonId = $reasonId;
        $this->rid = $rid;
    }
}
