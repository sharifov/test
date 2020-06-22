<?php

namespace sales\model\clientChatRequest\event;

use common\components\jobs\ClientChatJob;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\services\client\ClientManageService;
use yii\base\Component;
use yii\base\Event;

/**
 * Class ClientChatRequestEvents
 * @package sales\model\clientChatRequest\event
 */
class ClientChatRequestEvents extends Component
{
	public const CREATE = 'create';

	/**
	 * @param Event $event
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function createClientChatByApi(Event $event): void
	{
		/** @var ClientChatRequest */
		$clientChatRequest = $event->data;
		$clientChatRepository = \Yii::createObject(ClientChatRepository::class);
		$clientChat = $clientChatRepository->getOrCreateByRequest($clientChatRequest);

		$clientService = \Yii::createObject(ClientManageService::class);
		$client = $clientService->createByClientChatRequest($clientChatRequest);

		$clientChat->cch_client_id = $client->id;
		$clientChatRepository->save($clientChat);

		$job = new ClientChatJob();
		$job->priority = 1;
		$job->clientChat = $clientChat;
		\Yii::$app->queue_job->priority(90)->push($job);
	}
}