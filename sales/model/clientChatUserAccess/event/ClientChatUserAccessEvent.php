<?php
namespace sales\model\clientChatUserAccess\event;


use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use yii\base\Component;
use yii\base\Event;

/**
 * Class ClientChatUserAccessEvent
 * @package sales\model\clientChatUserAccess\event
 *
 */
class ClientChatUserAccessEvent extends Component
{
	public const SEND_NOTIFICATIONS = 'send_notification';

	public static function sendNotifications(Event $event): void
	{
		/**
		 * @var ClientChatUserAccess $access
		 */
		$access = $event->data;
		$data = [];
		if ($access->isAccept()) {
			$userAccessRepository = \Yii::createObject(ClientChatUserAccessRepository::class);
			$clientChatRepository = \Yii::createObject(ClientChatRepository::class);
			try {
				$clientChatRepository->assignOwner($access);
			} catch (\DomainException | \RuntimeException $e) {
				\Yii::error($e->getMessage(), 'ClientChatUserAccessEvent::sendNotifications');
				$userAccessRepository->updateStatus($access, ClientChatUserAccess::STATUS_SKIP);
				throw $e;
			}
			$userAccessRepository->disableAccessForOtherUsers($access);
			$data = ClientChatAccessMessage::accept($access);
		} else if ($access->isPending()) {
			$data = ClientChatAccessMessage::pending($access);
		} else if ($access->isSkip()) {
			$data = ClientChatAccessMessage::skip($access);
		}

		Notifications::publish('clientChatRequest', ['user_id' => $access->ccua_user_id], ['data' => $data]);
	}
}