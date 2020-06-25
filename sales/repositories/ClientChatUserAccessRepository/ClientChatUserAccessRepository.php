<?php
namespace sales\repositories\ClientChatUserAccessRepository;

use frontend\widgets\clientChat\ClientChatCache;
use sales\dispatchers\NativeEventDispatcher;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\ClientChatUserAccessEvent;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

class ClientChatUserAccessRepository extends Repository
{
	public function create(int $cchId, int $userId): void
	{
		$clientChatUserAccess = ClientChatUserAccess::create($cchId, $userId);
		$clientChatUserAccess->pending();
		$this->save($clientChatUserAccess);
	}

	public function save(ClientChatUserAccess $clientChatUserAccess): ClientChatUserAccess
	{
		if (!$clientChatUserAccess->save()) {
			throw new \RuntimeException($clientChatUserAccess->getErrorSummary(false)[0]);
		}

		ClientChatCache::invalidate($clientChatUserAccess->ccua_user_id);

		NativeEventDispatcher::recordEvent(ClientChatUserAccessEvent::class, ClientChatUserAccessEvent::SEND_NOTIFICATIONS, [ClientChatUserAccessEvent::class, 'sendNotifications'], $clientChatUserAccess);
		NativeEventDispatcher::triggerBy(ClientChatUserAccessEvent::class);

		return $clientChatUserAccess;
	}

	public function find(int $id): ClientChatUserAccess
	{
		if ($access = ClientChatUserAccess::findOne($id)) {
			return $access;
		}
		throw new NotFoundException('Client Chat User Access is not found');
	}

	public function updateStatus(ClientChatUserAccess $ccua, int $status): void
	{
		if (!ClientChatUserAccess::statusExist($status)) {
			throw new \RuntimeException('User access status is unknown');
		}
		$ccua->setStatus($status);
		$this->save($ccua);
	}

	public function disableAccessForOtherUsers(ClientChatUserAccess $ccua): void
	{
		$users = ClientChatUserAccess::find()->whichShouldBeDisabled($ccua->ccua_user_id, $ccua->ccua_cch_id)->all();
		foreach ($users as $user) {
			$this->updateStatus($user, ClientChatUserAccess::STATUS_SKIP);
		}
	}
}