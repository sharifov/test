<?php

namespace sales\services\clientChatUserAccessService;

use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;
use sales\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessService
 * @package sales\services\clientChatUserAccessService
 *
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
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

	public function __construct(ClientChatUserAccessRepository $clientChatUserAccessRepository, ClientChatRepository $clientChatRepository, ClientChatService $clientChatService)
	{
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientChatService = $clientChatService;
	}

	public function updateStatus(ClientChatUserAccess $ccua, int $status): void
	{
		if (!ClientChatUserAccess::statusExist($status)) {
			throw new \RuntimeException('User access status is unknown');
		}
		$ccua->setStatus($status);

		if ($ccua->isAccept()) {
			try {
				$ccua->ccuaCch->assignOwner($ccua->ccua_user_id);
				$this->clientChatRepository->save($ccua->ccuaCch);
//				$this->clientChatService->assignAgentToRcChannel($ccua->ccuaCch->cch_rid, $ccua->ccuaUser->userProfile->up_rc_user_id ?? '');
			} catch (\DomainException | \RuntimeException $e) {
				if (ClientChatCodeException::isRcAssignAgentFailed($e)) {
					$ccua->ccuaCch->removeOwner();
					$this->clientChatRepository->save($ccua->ccuaCch);
				}
				throw $e;
			}
			$this->disableAccessForOtherUsers($ccua);
		}
		$this->clientChatUserAccessRepository->save($ccua);
	}

	public function disableAccessForOtherUsers(ClientChatUserAccess $ccua): void
	{
		$usersAccess = ClientChatUserAccess::find()->exceptUser($ccua->ccua_user_id)->byChatId( $ccua->ccua_cch_id)->all();
		foreach ($usersAccess as $access) {
			$this->updateStatus($access, ClientChatUserAccess::STATUS_SKIP);
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