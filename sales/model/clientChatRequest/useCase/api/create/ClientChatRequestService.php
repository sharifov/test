<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatData\ClientChatDataRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\repositories\NotFoundException;
use sales\repositories\visitorLog\VisitorLogRepository;
use sales\services\client\ClientManageService;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;
use yii\helpers\Html;

/**
 * Class ClientChatRequestService
 * @package sales\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRequestRepository $clientChatRequestRepository
 * @property ClientChatRepository $clientChatRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatMessageRepository $clientChatMessageRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatService $clientChatService
 * @property VisitorLogRepository $visitorLogRepository
 * @property ClientChatDataRepository $clientChatDataRepository
 * @property TransactionManager $transactionManager
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
	 * @var ClientChatMessageService
	 */
	private ClientChatMessageService $clientChatMessageService;
	/**
	 * @var ClientChatService
	 */
	private ClientChatService $clientChatService;
	/**
	 * @var VisitorLogRepository
	 */
	private VisitorLogRepository $visitorLogRepository;
	/**
	 * @var ClientChatDataRepository
	 */
	private ClientChatDataRepository $clientChatDataRepository;
	/**
	 * @var TransactionManager
	 */
	private TransactionManager $transactionManager;

	/**
	 * ClientChatRequestService constructor.
	 * @param ClientChatRequestRepository $clientChatRequestRepository
	 * @param ClientChatRepository $clientChatRepository
	 * @param ClientManageService $clientManageService
	 * @param ClientChatMessageRepository $clientChatMessageRepository
	 * @param ClientChatMessageService $clientChatMessageService
	 * @param ClientChatService $clientChatService
	 * @param VisitorLogRepository $visitorLogRepository
	 * @param ClientChatDataRepository $clientChatDataRepository
	 * @param TransactionManager $transactionManager
	 */
	public function __construct(
		ClientChatRequestRepository $clientChatRequestRepository,
		ClientChatRepository $clientChatRepository,
		ClientManageService $clientManageService,
		ClientChatMessageRepository $clientChatMessageRepository,
		ClientChatMessageService $clientChatMessageService,
		ClientChatService $clientChatService,
		VisitorLogRepository $visitorLogRepository,
		ClientChatDataRepository $clientChatDataRepository,
		TransactionManager $transactionManager
	)
	{
		$this->clientChatRequestRepository = $clientChatRequestRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientManageService = $clientManageService;
		$this->clientChatMessageRepository = $clientChatMessageRepository;
		$this->clientChatMessageService = $clientChatMessageService;
		$this->clientChatService = $clientChatService;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->clientChatDataRepository = $clientChatDataRepository;
		$this->transactionManager = $transactionManager;
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @throws \JsonException
	 * @throws \Throwable
	 */
	public function create(ClientChatRequestApiForm $form): void
	{
		$clientChatRequest = $this->clientChatRequestRepository->create($form);

		$this->transactionManager->wrap( function () use ($clientChatRequest, $form) {
			if ($clientChatRequest->isGuestConnected()) {
				$this->guestConnected($clientChatRequest, $form);
			} else if ($clientChatRequest->isRoomConnected()) {
				$this->roomConnected($clientChatRequest);
			} else if ($clientChatRequest->isGuestDisconnected()) {
				$this->guestDisconnected($clientChatRequest);
			} else {
				throw new \RuntimeException('Unknown event provided');
			}
		});
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
		if (!$clientChat->cch_client_id) {
			$client = $this->clientManageService->createByClientChatRequest($clientChatRequest);
			$clientChat->cch_client_id = $client->id;
		}
		$this->clientChatRepository->save($clientChat);
		$this->saveAdditionalData($clientChat, $form);
	}

	private function guestDisconnected(ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->findNotClosed($clientChatRequest->ccr_rid);

		if ($clientChat->cch_client_online) {
			$clientChat->cch_client_online = 0;
			$this->clientChatRepository->save($clientChat);
			if ($clientChat->cch_owner_user_id) {
				Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
					'cchId' => $clientChat->cch_id,
					'isOnline' => (int)$clientChat->cch_client_online,
					'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
				]);
			}
		}
	}

	/**
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function roomConnected(ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->findNotClosed($clientChatRequest->ccr_rid);
		if (!$clientChat->cch_channel_id) {
			$this->clientChatService->assignToChannel($clientChat);
		}

		$clientChat->cch_client_online = 1;
		$this->clientChatRepository->save($clientChat);

		if ($clientChat->cch_owner_user_id) {
			Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
				'cchId' => $clientChat->cch_id,
				'isOnline' => (int)$clientChat->cch_client_online,
				'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
			]);
		}
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function saveMessage(ClientChatRequestApiForm $form, ClientChatRequest $clientChatRequest): void
	{
		try {
			$clientChat = $this->clientChatRepository->findNotClosed($form->data['rid'] ?? '');
		} catch (NotFoundException $e) {
			$clientChat = $this->clientChatRepository->findByRid($form->data['rid'] ?? '');

			$dto = ClientChatCloneDto::feelInOnCreateMessage($clientChat, $clientChatRequest->ccr_id);
			$clientChat = $this->clientChatRepository->clone($dto);
			$this->clientChatRepository->save($clientChat);
			$this->saveAdditionalData($clientChat, $form);
			$this->clientChatService->assignToChannel($clientChat);
		}

		$message = ClientChatMessage::createByApi($form, $clientChat, $clientChatRequest);
		$this->clientChatMessageRepository->save($message, 0);
		if ($clientChat->cch_owner_user_id && $clientChatRequest->isGuestUttered() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userProfile->isRegisteredInRc()) {
			$this->clientChatMessageService->increaseUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);
			$this->updateDateTimeLastMessageNotification($clientChat, $message);
		}
	}

	private function saveAdditionalData(ClientChat $clientChat, ClientChatRequestApiForm $form): void
	{
		if (!$this->visitorLogRepository->exist($clientChat->cch_id)) {
			$this->visitorLogRepository->createByClientChatRequest($clientChat, $form->data);
		}

		if (!$this->clientChatDataRepository->exist($clientChat->cch_id)) {
			$this->clientChatDataRepository->createByClientChatRequest($clientChat, $form->data);
		}
	}

	public function updateDateTimeLastMessageNotification(ClientChat $clientChat, ClientChatMessage $message): void
    {
        $user = $clientChat->cchOwnerUser;
        $dateTime = $message->ccm_sent_dt;
        $formatter = new \Yii::$app->formatter;
        if ($user->timezone) {
        	$formatter->timeZone = $user->timezone;
        }
        Notifications::publish('clientChatUpdateTimeLastMessage', ['user_id' => $clientChat->cch_owner_user_id], [
            'data' => [
                'dateTime' =>  $formatter->asRelativeTime(strtotime($dateTime)),
				'moment' =>  round((time() - strtotime($dateTime))),
				'cchId' => $clientChat->cch_id,
            ]
        ]);
    }
}