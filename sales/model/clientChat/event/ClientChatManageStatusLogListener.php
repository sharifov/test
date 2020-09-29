<?php


namespace sales\model\clientChat\event;


use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatManageStatusLogListener
 * @package sales\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatManageStatusLogListener
{
	/**
	 * @var ClientChatStatusLogService
	 */
	private ClientChatStatusLogService $chatStatusLogService;

	public function __construct(ClientChatStatusLogService $chatStatusLogService)
	{
		$this->chatStatusLogService = $chatStatusLogService;
	}

	public function handle(ClientChatManageStatusLogEvent $event): void
	{
		try {
			$this->chatStatusLogService->log(
				$event->chat->cch_id,
				$event->oldStatus,
				$event->newStatus,
				$event->ownerId,
				$event->description,
				$event->creatorUserId,
				$event->prevChannelId
			);
		} catch (\Throwable $e) {
			\Yii::error($e, 'ClientChatListener:ClientChatManageStatusLogListener');
		}
	}
}