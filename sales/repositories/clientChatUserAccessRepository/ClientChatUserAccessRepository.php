<?php
namespace sales\repositories\clientChatUserAccessRepository;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\SendNotificationEvent;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use sales\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessRepository
 * @package sales\repositories\clientChatUserAccessRepository
 *
 * @property ClientChatRepository $clientChatRepository
 * @property EventDispatcher $eventDispatcher
 */
class ClientChatUserAccessRepository extends Repository
{
	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;
	/**
	 * @var EventDispatcher
	 */
	private EventDispatcher $eventDispatcher;

	public function __construct(ClientChatRepository $clientChatRepository, EventDispatcher $eventDispatcher)
	{
		$this->clientChatRepository = $clientChatRepository;
		$this->eventDispatcher = $eventDispatcher;
	}

	public function save(ClientChatUserAccess $clientChatUserAccess): ClientChatUserAccess
	{
		if (!$clientChatUserAccess->save()) {
			throw new \RuntimeException($clientChatUserAccess->getErrorSummary(false)[0], ClientChatCodeException::CC_USER_ACCESS_SAVE_FAILED);
		}
//		$this->eventDispatcher->dispatch(new SendNotificationEvent($clientChatUserAccess), 'clientChatUserAccess_' . $clientChatUserAccess->ccua_user_id);
		return $clientChatUserAccess;
	}

	public function findByPrimaryKey(int $id): ClientChatUserAccess
	{
		if ($access = ClientChatUserAccess::findOne(['ccua_id' => $id])) {
			return $access;
		}
		throw new NotFoundException('Client Chat User Access is not found');
	}

	/**
	 * @param int $chatId
	 * @return ClientChatUserAccess[]
	 */
	public function findByChatIdAndUserId(int $chatId, int $userId): array
	{
		if ($access = ClientChatUserAccess::find()->byChatId($chatId)->byUserId($userId)->all()) {
			return $access;
		}
		throw new NotFoundException('Client Chat User Access not found rows');
	}

	public function sendNotifications(ClientChatUserAccess $access): void
	{
		$data = [];
		if ($access->isAccept()) {
			$data = ClientChatAccessMessage::accept($access);
		} else if ($access->isPending()) {
			$data = ClientChatAccessMessage::pending($access);
		} else if ($access->isSkip()) {
			$data = ClientChatAccessMessage::skip($access);
		}

		Notifications::publish('clientChatRequest', ['user_id' => $access->ccua_user_id], ['data' => $data]);
	}
}