<?php
namespace sales\repositories\clientChatUserAccessRepository;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\UpdateChatUserAccessWidgetEvent;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

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

	public function save(ClientChatUserAccess $access, ?ClientChat $chat = null): ClientChatUserAccess
	{
		if (!$access->save()) {
			throw new \RuntimeException($access->getErrorSummary(false)[0], ClientChatCodeException::CC_USER_ACCESS_SAVE_FAILED);
		}
		$this->eventDispatcher->dispatch(new UpdateChatUserAccessWidgetEvent($chat ?: $access->ccuaCch, $access->ccua_user_id, $access->ccua_status_id, $access->getPrimaryKey()), 'UpdateChatUserAccessWidgetEvent_' . $access->ccua_user_id);
		return $access;
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

	public function updateChatUserAccessWidget(ClientChat $chat, int $userId, int $statusId, ?int $ccuaId = null): void
	{
		$data = [];
		if ($statusId === ClientChatUserAccess::STATUS_ACCEPT) {
			$data = ClientChatAccessMessage::accept($chat->cch_id, $userId, $statusId);
		} else if ($statusId === ClientChatUserAccess::STATUS_PENDING) {
			$isChatInTransfer = $chat->isTransfer();
			$data = ClientChatAccessMessage::pending($chat->cch_id, $userId, $statusId, (int)$ccuaId, $isChatInTransfer);
		} else if ($statusId === ClientChatUserAccess::STATUS_SKIP) {
			$data = ClientChatAccessMessage::skip($chat->cch_id, $userId, $statusId);
		}

		Notifications::publish('clientChatRequest', ['user_id' => $userId], ['data' => $data]);
	}

	public function resetChatUserAccessWidget(int $userId): void
	{
		$data = ClientChatAccessMessage::reset($userId);
		Notifications::publish('clientChatRequest', ['user_id' => $userId], ['data' => $data]);
	}
}