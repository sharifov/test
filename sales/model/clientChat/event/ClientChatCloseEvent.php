<?php


namespace sales\model\clientChat\event;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatCloseEvent
 *
 * @property int $chatId
 * @property int|null $oldStatus
 * @property int|null $ownerId
 * @property int|null $creatorUserId
 * @property int|string $description
 * @property int|null $prevChannelId
 * @property int| $actionType
 * @property int|null $reasonId
 * @property bool $shallowClose
 * @property string|null $rid
 */
class ClientChatCloseEvent implements ClosedStatusGroupEventInterface
{
    public $chatId;
    public $oldStatus;
    public $ownerId;
    public $creatorUserId;
    public $description;
    public $prevChannelId;
    public $actionType;
    public $reasonId;
    public $shallowClose;
    public $rid;

    /**
     * @param int $chatId
     * @param int|null $oldStatus
     * @param int|null $ownerId
     * @param int|null $creatorUserId
     * @param string|null $description
     * @param int|null $prevChannelId
     * @param int $actionType
     * @param int|null $reasonId
     * @param bool $shallowClose
     * @param string|null $rid
     */
    public function __construct(
        int $chatId,
        ?int $oldStatus,
        ?int $ownerId,
        ?int $creatorUserId,
        ?string $description,
        ?int $prevChannelId,
        int $actionType,
        ?int $reasonId,
        bool $shallowClose = true,
        ?string $rid = null
    ) {
        $this->chatId = $chatId;
        $this->oldStatus = $oldStatus;
        $this->ownerId = $ownerId;
        $this->creatorUserId = $creatorUserId;
        $this->description = $description;
        $this->prevChannelId = $prevChannelId;
        $this->actionType = $actionType;
        $this->reasonId = $reasonId;
        $this->shallowClose = $shallowClose;
        $this->rid = $rid;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getOwnerId(): int
    {
        return (int)$this->ownerId;
    }

    public function getShallowCase(): bool
    {
        return $this->shallowClose;
    }
}
