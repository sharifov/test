<?php
namespace sales\repositories\ClientChatUserAccessRepository;

use frontend\widgets\clientChat\ClientChatCache;
use sales\dispatchers\NativeEventDispatcher;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\event\ClientChatUserAccessEvent;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

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
		\Yii::info('ClientChatUserAccessRepository1::save::ClientChatCache::invalidate userId: ' . $clientChatUserAccess->ccua_user_id, 'info\ClientChatUserAccessRepository::save');
		if (!$clientChatUserAccess->save()) {
			throw new \RuntimeException($clientChatUserAccess->getErrorSummary(false)[0]);
		}


//		$result = ClientChatCache::getCache()->getOrSet(ClientChatCache::getKey($clientChatUserAccess->ccua_user_id), function () use ($clientChatUserAccess) {
//			return [
//				'access' => ClientChatUserAccess::pendingRequests($clientChatUserAccess->ccua_user_id),
//			];
//		}, null, new TagDependency(['tags' => ClientChatCache::getTags($clientChatUserAccess->ccua_user_id)]));

//		\Yii::info(ArrayHelper::toArray($result), 'info\ClientChatUserAccessRepository::save');

		ClientChatCache::invalidate($clientChatUserAccess->ccua_user_id);

//		$result = ClientChatCache::getCache()->getOrSet(ClientChatCache::getKey($clientChatUserAccess->ccua_user_id), function () use ($clientChatUserAccess) {
//			return [
//				'access' => ClientChatUserAccess::pendingRequests($clientChatUserAccess->ccua_user_id),
//			];
//		}, null, new TagDependency(['tags' => ClientChatCache::getTags($clientChatUserAccess->ccua_user_id)]));
//
//		\Yii::info(ArrayHelper::toArray($result), 'info\ClientChatUserAccessRepository::save');

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