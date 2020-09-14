<?php

namespace sales\services\clientChatUserAccessService;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\dispatchers\EventDispatcher;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\ResetChatUserAccessWidgetEvent;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\repositories\clientChatUserChannel\ClientChatUserChannelRepository;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatUserAccessService
 * @package sales\services\clientChatUserAccessService
 *
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 * @property ClientChatUserChannelRepository $clientChatUserChannelRepository
 * @property TransactionManager $transactionManager
 */
class ClientChatUserAccessService
{
	/**
	 * @var ClientChatUserAccessRepository
	 */
	private ClientChatUserAccessRepository $clientChatUserAccessRepository;
	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;
	/**
	 * @var ClientChatService
	 */
	private ClientChatService $clientChatService;
	/**
	 * @var ClientChatVisitorRepository
	 */
	private ClientChatVisitorRepository $clientChatVisitorRepository;
	/**
	 * @var ClientChatUserChannelRepository
	 */
	private ClientChatUserChannelRepository $clientChatUserChannelRepository;
	/**
	 * @var TransactionManager
	 */
	private TransactionManager $transactionManager;

	public function __construct(
		ClientChatUserAccessRepository $clientChatUserAccessRepository,
		ClientChatRepository $clientChatRepository,
		ClientChatService $clientChatService,
		ClientChatVisitorRepository $clientChatVisitorRepository,
		ClientChatUserChannelRepository $clientChatUserChannelRepository,
		TransactionManager $transactionManager
	) {
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatService = $clientChatService;
		$this->clientChatVisitorRepository = $clientChatVisitorRepository;
		$this->clientChatUserChannelRepository = $clientChatUserChannelRepository;
		$this->transactionManager = $transactionManager;
	}

	public function updateStatus(ClientChatUserAccess $ccua, int $status, ?int $chatOwnerId = null): void
	{
		if (!ClientChatUserAccess::statusExist($status)) {
			throw new \RuntimeException('User access status is unknown');
		}
		$ccua->setStatus($status);

		$clientChat = $this->clientChatRepository->findById($ccua->ccua_cch_id);
		$previousOwner = $clientChat->cch_owner_user_id;
		if ($ccua->isAccept()) {
			try {
				if ($clientChat->isTransfer()) {
					$this->clientChatService->finishTransfer($clientChat, $ccua);
				} else {
					$clientChat->assignOwner($ccua->ccua_user_id);
					$this->clientChatRepository->save($clientChat);
					$this->clientChatService->assignAgentToRcChannel($clientChat->cch_rid, $ccua->ccuaUser->userProfile->up_rc_user_id ?? '');
				}
			} catch (\DomainException | \RuntimeException $e) {
				if (ClientChatCodeException::isRcAssignAgentFailed($e)) {
					$ccua->ccuaCch->assignOwner($previousOwner);
					$this->clientChatRepository->save($ccua->ccuaCch);
				}
				throw $e;
			}
			$this->disableAccessForOtherUsers($ccua->ccua_cch_id, $ccua->ccua_user_id);
		} else if ($ccua->isSkip() && $clientChat->isTransfer()) {
			$userAccesses = ClientChatUserAccess::find()->byChatId($clientChat->cch_id)->exceptById($ccua->ccua_id)->pending()->exists();
			if (!$userAccesses) {
				$this->clientChatService->cancelTransfer($clientChat);

				if ($chatOwnerId !== $clientChat->cch_owner_user_id) {
					$data = ClientChatAccessMessage::allAgentsCanceledTransfer($clientChat);
					Notifications::publish('clientChatTransfer', ['user_id' => $clientChat->cch_owner_user_id], ['data' => $data]);
				}
			}
		}
		$this->clientChatUserAccessRepository->save($ccua);
	}

	public function disableAccessForOtherUsers(int $chatId, int $userId): void
	{
		$usersAccess = ClientChatUserAccess::find()->notAccepted()->exceptUser($userId)->byChatId($chatId)->all();
		foreach ($usersAccess as $access) {
			$this->updateStatus($access, ClientChatUserAccess::STATUS_SKIP, $userId);
		}
	}

	/**
	 * @param int $userId
	 * @throws \Throwable
	 */
	public function disableUserAccessToAllChats(int $userId): void
	{
		$this->transactionManager->wrap( static function () use ($userId) {
			/** @var ClientChatUserAccess[] $userAccess  */
			$eventDispatcher = \Yii::createObject(EventDispatcher::class);
			$userAccess = ClientChatUserAccess::find()->select(['ccua_cch_id'])->byUserId($userId)->pending()->asArray()->all();
			$chats = ArrayHelper::getColumn($userAccess, 'ccua_cch_id');
			ClientChatUserAccess::updateAll(['ccua_status_id' => ClientChatUserAccess::STATUS_SKIP], ['ccua_cch_id' => $chats, 'ccua_user_id' => $userId, 'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING]);
			$eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
		});
	}

	/**
	 * @param ClientChatUserChannel[] $userChannels
	 * @throws \Throwable
	 */
	public function setUserAccessToAllChats(array $userChannels): void
	{
		$_self = $this;
		$this->transactionManager->wrap( static function () use ($userChannels, $_self) {
			foreach ($userChannels as $userChannel) {
				if ($chats = ClientChat::find()->byOwner(null)->byChannel($userChannel->ccuc_channel_id)->all()) {
					foreach ($chats as $chat) {
						$_self->clientChatService->sendRequestToUser($chat, $userChannel);
					}
				}
			}
		});
	}

	public function setUserAccessToAllChatsByChannelIds(array $channelIds, int $userId)
	{
		$eventDispatcher = \Yii::createObject(EventDispatcher::class);
		if ($chats = ClientChat::find()->select(['cch_id'])->byOwner(null)->byChannelIds($channelIds)->asArray()->all()) {
			$data = [];
			foreach ($chats as $chat) {
				$data[] = [
					'ccua_cch_id' => $chat['cch_id'],
					'ccua_user_id' => $userId,
					'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING,
					'ccua_created_dt' => date('Y-m-d H:i:s'),
					'ccua_updated_dt' => date('Y-m-d H:i:s'),
				];
			}
			\Yii::$app->db->createCommand()->batchInsert(ClientChatUserAccess::tableName(), ['ccua_cch_id', 'ccua_user_id', 'ccua_status_id', 'ccua_created_dt', 'ccua_updated_dt'], $data)->execute();
		}
		$eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
	}

	public function disableUserAccessToAllChatsByChannelIds(array $channelIds, int $userId): void
	{
		$eventDispatcher = \Yii::createObject(EventDispatcher::class);
		if ($chats = ClientChat::find()->select(['cch_id'])->byOwner(null)->byChannelIds($channelIds)->asArray()->all()) {
			$chats = ArrayHelper::getColumn($chats, 'cch_id');
			ClientChatUserAccess::updateAll(['ccua_status_id' => ClientChatUserAccess::STATUS_SKIP], ['ccua_cch_id' => $chats, 'ccua_status_id' => ClientChatUserAccess::STATUS_PENDING, 'ccua_user_id' => $userId]);
		}
		$eventDispatcher->dispatch(new ResetChatUserAccessWidgetEvent($userId), 'ResetChatUserAccessWidgetEvent_' . $userId);
	}
}