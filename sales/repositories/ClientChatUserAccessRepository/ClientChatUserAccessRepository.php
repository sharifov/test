<?php
namespace sales\repositories\ClientChatUserAccessRepository;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class ClientChatUserAccessRepository
 * @package sales\repositories\ClientChatUserAccessRepository
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

		$this->sendNotifications($clientChatUserAccess);

//		ClientChatCache::invalidate($clientChatUserAccess->ccua_user_id);

		return $clientChatUserAccess;
	}

	public function findByPrimaryKeys(int $cchId, int $userId): ClientChatUserAccess
	{
		if ($access = ClientChatUserAccess::findOne(['ccua_cch_id' => $cchId, 'ccua_user_id' => $userId])) {
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

	public function sendNotifications(ClientChatUserAccess $access): void
	{
		$data = [];
		if ($access->isAccept()) {
			try {
				$this->clientChatRepository->assignOwner($access);
			} catch (\DomainException | \RuntimeException $e) {
				\Yii::error($e->getMessage(), 'ClientChatUserAccessEvent::sendNotifications');
				$this->updateStatus($access, ClientChatUserAccess::STATUS_SKIP);
				throw $e;
			}
			$this->disableAccessForOtherUsers($access);

			$data = ClientChatAccessMessage::accept($access);
		} else if ($access->isPending()) {
			$data = ClientChatAccessMessage::pending($access);
		} else if ($access->isSkip()) {
			$data = ClientChatAccessMessage::skip($access);
		}

		Notifications::publish('clientChatRequest', ['user_id' => $access->ccua_user_id], ['data' => $data]);
	}
}