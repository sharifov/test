<?php

namespace sales\services\clientChatUserAccessService;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessService
 * @package sales\services\clientChatUserAccessService
 *
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
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

	public function __construct(ClientChatUserAccessRepository $clientChatUserAccessRepository, ClientChatRepository $clientChatRepository, ClientChatService $clientChatService, ClientChatVisitorRepository $clientChatVisitorRepository)
	{
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatService = $clientChatService;
		$this->clientChatVisitorRepository = $clientChatVisitorRepository;
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
	 */
	public function removeUserAccess(int $userId): void
	{
		/** @var ClientChatUserAccess[] $userAccess  */
		$userAccess = ClientChatUserAccess::find()->byUserId($userId)->pending()->all();
		foreach ($userAccess as $access) {
			$this->updateStatus($access, ClientChatUserAccess::STATUS_SKIP);
		}
	}
}