<?php


namespace sales\services\clientChatService;


use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class ClientChatStatusLogService
 * @package sales\services\clientChatService
 *
 * @property ClientChatStatusLogRepository $clientChatStatusLogRepository
 */
class ClientChatStatusLogService
{
	/**
	 * @var ClientChatStatusLogRepository
	 */
	private ClientChatStatusLogRepository $clientChatStatusLogRepository;

	public function __construct(ClientChatStatusLogRepository $clientChatStatusLogRepository)
	{

		$this->clientChatStatusLogRepository = $clientChatStatusLogRepository;
	}

	public function log(int $chatId, ?int $fromStatus, int $toStatus, ?int $ownerId, ?string $description, ?int $userId, ?int $prevChannel): void
	{
		if ($previous = $this->clientChatStatusLogRepository->getPrevious($chatId)) {
			$previous->end();
			$this->clientChatStatusLogRepository->save($previous);
		}
		$log = ClientChatStatusLog::create($chatId, $fromStatus, $toStatus, $ownerId, $userId, $prevChannel, $description);
		$this->clientChatStatusLogRepository->save($log);
	}
}