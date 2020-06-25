<?php
namespace sales\model\clientChatUserAccess\event;


use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use frontend\widgets\clientChat\ClientChatCache;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatUserAccessEvent
 * @package sales\model\clientChatUserAccess\event
 *
 */
class ClientChatUserAccessEvent extends Component
{
	public const SEND_NOTIFICATIONS = 'send_notification';

	/**
	 * @var ClientChatUserAccess $access
	 */
	private static ClientChatUserAccess $access;

	public static function sendNotifications(Event $event): void
	{
		self::$access = $event->data;

		\Yii::info(ArrayHelper::toArray(self::$access), 'info\ClientChatUserAccessEvent::sendNotifications');

		$data = [];
		if (self::$access->isAccept()) {
			$userAccessRepository = \Yii::createObject(ClientChatUserAccessRepository::class);
			$clientChatRepository = \Yii::createObject(ClientChatRepository::class);
			$clientChatRepository->assignOwner(self::$access);
			$userAccessRepository->disableAccessForOtherUsers(self::$access);

			$data = ClientChatAccessMessage::accept(self::$access);
		} else if (self::$access->isPending()) {
			$data = ClientChatAccessMessage::pending(self::$access);
		} else if (self::$access->isSkip()) {
			$data = ClientChatAccessMessage::skip(self::$access);
		}

		Notifications::publish('clientChatRequest', ['user_id' => self::$access->ccua_user_id], ['data' => $data]);
	}
}