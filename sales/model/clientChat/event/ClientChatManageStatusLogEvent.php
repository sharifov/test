<?php


namespace sales\model\clientChat\event;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class ClientChatManageStatusLogEvent
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
 */
class ClientChatManageStatusLogEvent
{
	public $chat;
	public $oldStatus;
	public $newStatus;
	public $ownerId;
	public $creatorUserId;
	public $description;
	public $prevChannelId;
	public $actionType;

	public function __construct(ClientChat $chat, ?int $oldStatus, int $newStatus, ?int $ownerId, ?int $creatorUserId, ?string $description, ?int $prevChannelId, int $actionType)
	{
		$this->chat = $chat;
		$this->oldStatus = $oldStatus;
		$this->newStatus = $newStatus;
		$this->ownerId = $ownerId;
		$this->creatorUserId = $creatorUserId;
		$this->description = $description;
		$this->prevChannelId = $prevChannelId;
		$this->actionType = $actionType;
	}
}