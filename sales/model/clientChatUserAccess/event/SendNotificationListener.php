<?php

namespace sales\model\clientChatUserAccess\event;

use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;

/**
 * Class SendNotificationListener
 * @package sales\model\clientChatUserAccess\event
 *
 * @property ClientChatUserAccessRepository $accessRepository
 */
class SendNotificationListener
{
	public $accessRepository;

	public function __construct(ClientChatUserAccessRepository $accessRepository)
	{
		$this->accessRepository = $accessRepository;
	}

	public function handle(SendNotificationEvent $event): void
	{
		$this->accessRepository->sendNotifications($event->userAccess);
	}
}