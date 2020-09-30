<?php

namespace sales\model\clientChatUserAccess\event;

use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;

/**
 * Class UpdateChatUserAccessWidgetListener
 * @package sales\model\clientChatUserAccess\event
 *
 * @property ClientChatUserAccessRepository $accessRepository
 */
class UpdateChatUserAccessWidgetListener
{
	public $accessRepository;

	public function __construct(ClientChatUserAccessRepository $accessRepository)
	{
		$this->accessRepository = $accessRepository;
	}

	public function handle(UpdateChatUserAccessWidgetEvent $event): void
	{
		$this->accessRepository->updateChatUserAccessWidget($event->chat, $event->userId, $event->statusId, $event->ccuaId);
	}
}