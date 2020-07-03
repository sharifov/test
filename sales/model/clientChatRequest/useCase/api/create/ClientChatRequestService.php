<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\components\jobs\ClientChatJob;
use common\models\VisitorLog;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatData\entity\ClientChatData;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\services\client\ClientManageService;
use yii\helpers\VarDumper;

/**
 * Class ClientChatRequestService
 * @package sales\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatMessageRepository $clientChatMessageRepository
 */
class ClientChatRequestService
{

	/**
	 * @var ClientChatRequestRepository
	 */
	private ClientChatRequestRepository $clientChatRequestRepository;
	/**
	 * @var ClientChatRepository
	 */
	private ClientChatRepository $clientChatRepository;
	/**
	 * @var ClientManageService
	 */
	private ClientManageService $clientManageService;
	/**
	 * @var ClientChatMessageRepository
	 */
	private ClientChatMessageRepository $clientChatMessageRepository;

	/**
	 * ClientChatRequestService constructor.
	 * @param ClientChatRequestRepository $clientChatRequestRepository
	 * @param ClientChatRepository $clientChatRepository
	 * @param ClientManageService $clientManageService
	 * @param ClientChatMessageRepository $clientChatMessageRepository
	 */
	public function __construct(
		ClientChatRequestRepository $clientChatRequestRepository,
		ClientChatRepository $clientChatRepository,
		ClientManageService $clientManageService,
		ClientChatMessageRepository $clientChatMessageRepository
	)
	{
		$this->clientChatRequestRepository = $clientChatRequestRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientManageService = $clientManageService;
		$this->clientChatMessageRepository = $clientChatMessageRepository;
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @throws \JsonException
	 */
	public function create(ClientChatRequestApiForm $form): void
	{
		$clientChatRequest = $this->clientChatRequestRepository->create($form);

		if ($clientChatRequest->isGuestConnected()) {
			$this->guestConnected($clientChatRequest, $form);
		} else if ($clientChatRequest->isRoomConnected()) {
			$this->roomConnected($clientChatRequest);
		} else {
			throw new \RuntimeException('Unknown event provided');
		}
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @throws \JsonException
	 */
	public function createMessage(ClientChatRequestApiForm $form): void
	{
		$clientChatRequest = $this->clientChatRequestRepository->create($form);

		if ($clientChatRequest->isGuestUttered() || $clientChatRequest->isAgentUttered()) {
			$this->saveMessage($form, $clientChatRequest);
		} else {
			throw new \RuntimeException('Unknown event provided');
		}
	}

	/**
	 * @param ClientChatRequest $clientChatRequest
	 * @param ClientChatRequestApiForm $form
	 */
	private function guestConnected(ClientChatRequest $clientChatRequest, ClientChatRequestApiForm $form): void
	{
		$clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest);
		$client = $this->clientManageService->createByClientChatRequest($clientChatRequest);
		$clientChat->cch_client_id = $client->id;
		$this->clientChatRepository->save($clientChat);

		$visitorLog = VisitorLog::createByClientChatRequest($clientChat, $form->data);
		if (!$visitorLog->validate()) {
			foreach ($visitorLog->errors as $attribute => $error) {
				$visitorLog->{$attribute} = null;
			}
			\Yii::error('VisitorLog validation failed: ' . VarDumper::dumpAsString($visitorLog->errors), 'ClientChatRequestService::guestConnected::visitorLog::validation');
		}

		if (!$visitorLog->save(false)) {
			\Yii::error('VisitorLog save failed: ' . VarDumper::dumpAsString($visitorLog->errors), 'ClientChatRequestService::guestConnected::visitorLog::save');
		}


		if (!ClientChatData::find()->byCchId($clientChat->cch_id)->exists()) {
			$clientChatData = ClientChatData::createByClientChatRequest($clientChat->cch_id, $form->data);
			if (!$clientChatData->validate()) {
				foreach ($clientChatData->errors as $attribute => $error) {
					$clientChatData->{$attribute} = null;
				}
				\Yii::error('ClientChatData validation failed: ' . VarDumper::dumpAsString($clientChatData->errors), 'ClientChatRequestService::guestConnected::clientChatData::validation');
			}

			if (!$clientChatData->save(false)) {
				\Yii::error('ClientChatData save failed: ' . VarDumper::dumpAsString($clientChatData->errors), 'ClientChatRequestService::guestConnected::clientChatData::save');
			}
		}
	}

	/**
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function roomConnected(ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest);

		$job = new ClientChatJob();
		$job->priority = 1;
		$job->clientChat = $clientChat;
		\Yii::$app->queue_job->priority(90)->push($job);
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function saveMessage(ClientChatRequestApiForm $form, ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->findByRid($form->data['rid'] ?? '');
		$message = ClientChatMessage::createByApi($form, $clientChat, $clientChatRequest);
		$this->clientChatMessageRepository->save($message, 0);
	}
}