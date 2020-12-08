<?php

namespace sales\model\clientChat\event;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatPendingEvent
 * @package sales\model\clientChat\event
 *
 * @property ClientChat $chat
 * @property int|null $oldStatus
 * @property int $newStatus
 * @property int|null $ownerId
 * @property int|null $creatorUserId
 * @property int|string $description
 * @property int|null $prevChannelId
 * @property int| $actionType
 * @property int|null $reasonId
 * @property string|null $rid
 */
class ClientChatPendingEvent
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
