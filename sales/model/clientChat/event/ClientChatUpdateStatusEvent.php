<?php

namespace sales\model\clientChat\event;

/**
 * Class ClientChatUpdateStatusEvent
 * @package sales\model\clientChat\event
 *
 * @property int $chatId
 * @property int|null $oldStatusId
 * @property int $newStatusId
 * @property int $userId
 * @property int $actionType
 * @property int|null $prevChannelId
 */
class ClientChatUpdateStatusEvent
{
    public $chatId;
    public $oldStatusId;
    public $newStatusId;
    public $userId;
    public $actionType;
    public $prevChannelId;

    public function __construct(
        int $chatId,
        ?int $oldStatusId,
        int $newStatusId,
        int $userId,
        int $actionType,
        ?int $prevChannelId
    ) {
        $this->chatId = $chatId;
        $this->oldStatusId = $oldStatusId;
        $this->newStatusId = $newStatusId;
        $this->userId = $userId;
        $this->actionType = $actionType;
        $this->prevChannelId = $prevChannelId;
    }
}
