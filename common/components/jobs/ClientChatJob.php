<?php
namespace common\components\jobs;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\repositories\NotFoundException;
use sales\services\clientChatService\ClientChatService;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class ClientChatJob
 * @package common\components\jobs
 *
 * @property int $priority
 * @property ClientChat $clientChat
 * @property ClientChatService $clientChatService
 * @property ClientChatRepository $clientChatRepository
 */
class ClientChatJob extends BaseObject implements JobInterface
{
	public int $priority = 1;

	/**
	 * @var ClientChat
	 */
	public ClientChat $clientChat;

	/**
	 * @var ClientChatService
	 */
	private ClientChatService $clientChatService;

	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;

	public function execute($queue)
	{
		$this->clientChatService = \Yii::createObject(ClientChatService::class);
		$this->clientChatRepository = \Yii::createObject(ClientChatRepository::class);

		try {
			$this->clientChatService->assignClientChatChannel($this->clientChat, $this->priority);
			$this->clientChatRepository->save($this->clientChat);
			$this->clientChatService->sendNotificationToUsers($this->clientChat);
		} catch (\RuntimeException | NotFoundException $e) {
			\Yii::info('ClientChatJob failed... ' . $e->getMessage() . '; File: ' . $e->getFile() . '; Line: ' . $e->getLine(), 'info\ClientChatJob::execute');
			return false;
		}

		\Yii::info('ClientChatJob successfully finished...', 'info\ClientChatJob::execute');
		return true;
	}
}