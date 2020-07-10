<?php
namespace sales\repositories\clientChatUserAccessRepository;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use sales\services\clientChatService\ClientChatService;

/**
 * Class ClientChatUserAccessRepository
 * @package sales\repositories\clientChatUserAccessRepository
 *
 * @property ClientChatRepository $clientChatRepository
 */
class ClientChatUserAccessRepository extends Repository
{
	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;

	public function __construct(ClientChatRepository $clientChatRepository)
	{
		$this->clientChatRepository = $clientChatRepository;
	}

	public function save(ClientChatUserAccess $clientChatUserAccess): ClientChatUserAccess
	{
		if (!$clientChatUserAccess->save()) {
			throw new \RuntimeException($clientChatUserAccess->getErrorSummary(false)[0], ClientChatCodeException::CC_USER_ACCESS_SAVE_FAILED);
		}

		if ($clientChatUserAccess->ccuaUser->userProfile && $clientChatUserAccess->ccuaUser->userProfile->isRegisteredInRc()) {
			$this->sendNotifications($clientChatUserAccess);
		}
		return $clientChatUserAccess;
	}

	public function findByPrimaryKeys(int $cchId, int $userId): ClientChatUserAccess
	{
		if ($access = ClientChatUserAccess::findOne(['ccua_cch_id' => $cchId, 'ccua_user_id' => $userId])) {
			return $access;
		}
		throw new NotFoundException('Client Chat User Access is not found');
	}

	private function sendNotifications(ClientChatUserAccess $access): void
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