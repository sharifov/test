<?php
namespace sales\services\clientChatService;

use http\Exception\RuntimeException;
use sales\model\clientChat\ClientChatCodeException;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\repositories\ClientChatUserAccessRepository\ClientChatUserAccessRepository;
use yii\helpers\Json;

/**
 * Class ClientChatService
 * @package sales\services\clientChatService
 *
 * @property ClientChatChannelRepository $clientChatChannelRepository
 */
class ClientChatService
{
	/**
	 * @var ClientChatChannelRepository
	 */
	private ClientChatChannelRepository $clientChatChannelRepository;

	public function __construct(ClientChatChannelRepository $clientChatChannelRepository)
	{
		$this->clientChatChannelRepository = $clientChatChannelRepository;
	}

	public function assignClientChatChannel(ClientChat $clientChat, int $priority): void
	{
		$clientChatChannel = $this->clientChatChannelRepository->findByClientChatData($clientChat, $priority);
		$clientChat->cch_channel_id = $clientChatChannel->ccc_id;
	}

	/**
	 * @param ClientChat $clientChat
	 * @param ClientChatUserAccessRepository $clientChatUserAccessRepository
	 */
	public function sendNotificationToUsers(ClientChat $clientChat, ClientChatUserAccessRepository $clientChatUserAccessRepository): void
	{
		if ($channel = $clientChat->cchChannel) {
			$userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all();

			if ($userChannel) {
				/** @var ClientChatUserChannel $user */
				foreach ($userChannel as $user) {
					$access = $clientChatUserAccessRepository->create($clientChat->cch_id, $user->ccuc_user_id);
					$clientChatUserAccessRepository->save($access);
				}
			}
		}
	}

	/**
	 * @param string $rid
	 * @param string $userId
	 * @throws \yii\httpclient\Exception
	 */
	public function assignAgentToRcChannel(string $rid, string $userId): void
	{
		$response = \Yii::$app->chatBot->assignAgent($rid, $userId);
		if ($response['error']) {
			$error = Json::decode($response['error']);
			throw new \RuntimeException($error['data']['error'], ClientChatCodeException::RC_ASSIGN_AGENT_FAILED);
		}
	}
}