<?php
namespace sales\services\clientChatService;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use yii\helpers\VarDumper;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 * @property ClientChatUserAccessRepository $clientChatUserAccessRepository
 */
class ClientChatService
{
	/**
	 * @var ClientChatChannelRepository
	 */
	private ClientChatChannelRepository $clientChatChannelRepository;
	/**
	 * @var ClientChatUserAccessRepository
	 */
	private ClientChatUserAccessRepository $clientChatUserAccessRepository;

	public function __construct(ClientChatChannelRepository $clientChatChannelRepository, ClientChatUserAccessRepository $clientChatUserAccessRepository)
	{
		$this->clientChatChannelRepository = $clientChatChannelRepository;
		$this->clientChatUserAccessRepository = $clientChatUserAccessRepository;
	}

	public function assignClientChatChannel(ClientChat $clientChat, int $priority): void
	{
		$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($clientChat, $priority);
		$clientChat->cch_channel_id = $clientChatChannel->ccc_id;
	}

	/**
	 * @param ClientChat $clientChat
	 */
	public function sendNotificationToUsers(ClientChat $clientChat): void
	{
		if ($channel = $clientChat->cchChannel) {
			$userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all();

			if ($userChannel) {
				/** @var ClientChatUserChannel $user */
				foreach ($userChannel as $user) {

					\Yii::error('clientChatUserAccessRepository::create');
					$this->clientChatUserAccessRepository->create($clientChat->cch_id, $user->ccuc_user_id);

//					if ($ntf = Notifications::create($user->ccuc_user_id, $title, $message, Notifications::TYPE_INFO, true)) {
//						$dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
//						Notifications::publish('getNewNotification', ['user_id' => $user->ccuc_user_id], $dataNotification);
//					}
				}
			}
		}
	}
}