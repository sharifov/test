<?php

namespace sales\model\clientChatUserAccess\event;

use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;

/**
 * Class UpdateChatUserAccessWidgetEvent
 * @package sales\model\clientChatUserAccess\event
 *
 * @property int $cchId
 * @property int $userId
 * @property int $statusId
 * @property int|null $ccuaId
 */
class UpdateChatUserAccessWidgetEvent
{
	public $cchId;

	public $userId;

	public $statusId;

	public $ccuaId;

	public function __construct(int $cchId, int $userId, int $statusId, ?int $ccuaId = null)
	{
		$this->cchId = $cchId;
		$this->userId = $userId;
		$this->statusId = $statusId;
		$this->ccuaId = $ccuaId;
	}
}