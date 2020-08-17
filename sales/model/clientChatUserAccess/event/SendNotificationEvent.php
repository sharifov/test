<?php

namespace sales\model\clientChatUserAccess\event;

use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;

/**
 * Class SendNotificationEvent
 * @package sales\model\clientChatUserAccess\event
 *
 * @property ClientChatUserAccess $userAccess
 */
class SendNotificationEvent
{
	public $userAccess;

	public function __construct(ClientChatUserAccess $access)
	{
		$this->userAccess = $access;
	}
}