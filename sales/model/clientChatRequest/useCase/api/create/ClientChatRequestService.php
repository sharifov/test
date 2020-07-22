<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitor\repository\ClientChatVisitorRepository;
use sales\model\clientChatVisitorData\repository\ClientChatVisitorDataRepository;
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
 * @property TransactionManager $transactionManager
 * @property ClientChatVisitorRepository $clientChatVisitorRepository
 * @property ClientChatVisitorDataRepository $clientChatVisitorDataRepository
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
	 * @var TransactionManager
	 */
	private TransactionManager $transactionManager;
	/**
	 * @var ClientChatVisitorRepository
	 */
	private ClientChatVisitorRepository $clientChatVisitorRepository;
	/**
	 * @var ClientChatVisitorDataRepository
	 */
	private ClientChatVisitorDataRepository $clientChatVisitorDataRepository;

	/**
	 * ClientChatRequestService constructor.
	 * @param ClientChatRequestRepository $clientChatRequestRepository
	 * @param ClientChatRepository $clientChatRepository
	 * @param ClientManageService $clientManageService
	 * @param ClientChatMessageRepository $clientChatMessageRepository
	 * @param ClientChatMessageService $clientChatMessageService
	 * @param ClientChatService $clientChatService
	 * @param VisitorLogRepository $visitorLogRepository
	 * @param TransactionManager $transactionManager
	 * @param ClientChatVisitorRepository $clientChatVisitorRepository
	 * @param ClientChatVisitorDataRepository $clientChatVisitorDataRepository
	 */
	public function __construct(
		ClientChatRequestRepository $clientChatRequestRepository,
		ClientChatRepository $clientChatRepository,
		ClientManageService $clientManageService,
		ClientChatMessageRepository $clientChatMessageRepository,
		ClientChatMessageService $clientChatMessageService,
		ClientChatService $clientChatService,
		VisitorLogRepository $visitorLogRepository,
		TransactionManager $transactionManager,
		ClientChatVisitorRepository $clientChatVisitorRepository,
		ClientChatVisitorDataRepository $clientChatVisitorDataRepository
	)
	{
		$this->clientChatRequestRepository = $clientChatRequestRepository;
		$this->clientChatRepository = $clientChatRepository;
		$this->clientManageService = $clientManageService;
		$this->clientChatMessageRepository = $clientChatMessageRepository;
		$this->clientChatMessageService = $clientChatMessageService;
		$this->clientChatService = $clientChatService;
		$this->visitorLogRepository = $visitorLogRepository;
		$this->transactionManager = $transactionManager;
		$this->clientChatVisitorRepository = $clientChatVisitorRepository;
		$this->clientChatVisitorDataRepository = $clientChatVisitorDataRepository;
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @throws \JsonException
	 * @throws \Throwable
	 */
	public function create(ClientChatRequestApiForm $form): void
	{
		$clientChatRequest = ClientChatRequest::createByApi($form);
		$this->clientChatRequestRepository->save($clientChatRequest);

		$this->transactionManager->wrap( function () use ($clientChatRequest, $form) {
			if ($clientChatRequest->isRoomConnected()) {
				$this->roomConnected($clientChatRequest, $form);
			} else if ($clientChatRequest->isGuestDisconnected()) {
				$this->guestDisconnected($clientChatRequest);
			} else if ($clientChatRequest->isTrackEvent()) {
				$this->createOrUpdateVisitorData($form, $clientChatRequest);
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
		$clientChatRequest = ClientChatRequest::createByApi($form);
		$this->clientChatRequestRepository->save($clientChatRequest);

		if ($clientChatRequest->isGuestUttered() || $clientChatRequest->isAgentUttered()) {
			$this->saveMessage($form, $clientChatRequest);
		} else {
			throw new \RuntimeException('Unknown event provided');
		}
	}

	private function guestDisconnected(ClientChatRequest $clientChatRequest): void
	{
		$visitor = $this->clientChatVisitorRepository->findByVisitorId($clientChatRequest->getClientRcId());

		if ($visitor->ccvClient) {
			$clientChats = $this->clientChatRepository->findByClientId($visitor->ccv_client_id);

			foreach ($clientChats as $clientChat) {
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
		}
	}

	/**
	 * @param ClientChatRequest $clientChatRequest
	 * @param ClientChatRequestApiForm $form
	 */
	private function roomConnected(ClientChatRequest $clientChatRequest, ClientChatRequestApiForm $form): void
	{
		$clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest);
		if (!$clientChat->cch_client_id) {
			$client = $this->clientManageService->getOrCreateByClientChatRequest($clientChatRequest);
			$clientChat->cch_client_id = $client->id;
		}

		if (!$clientChat->cch_channel_id) {
			$this->clientChatService->assignToChannel($clientChat);
		}

		$clientChat->cch_client_online = 1;
		$this->clientChatRepository->save($clientChat);

		$visitorRcId = $clientChatRequest->getClientRcId();

		$visitorData = $this->clientChatVisitorDataRepository->findOrCreateByVisitorId($visitorRcId);
		$this->clientChatVisitorRepository->create($clientChat->cch_id, $visitorData->cvd_id, $clientChat->cch_client_id);

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
			$this->clientChatService->assignToChannel($clientChat);
		}

		$message = ClientChatMessage::createByApi($form, $clientChat, $clientChatRequest);
		$this->clientChatMessageRepository->save($message, 0);
		if ($clientChat->cch_owner_user_id && $clientChatRequest->isGuestUttered() && $clientChat->cchOwnerUser->userProfile && $clientChat->cchOwnerUser->userProfile->isRegisteredInRc()) {
			$this->clientChatMessageService->increaseUnreadMessages($clientChat->cch_id, $clientChat->cch_owner_user_id);
			$this->updateDateTimeLastMessageNotification($clientChat, $message);
		}
	}

	public function createOrUpdateVisitorData(ClientChatRequestApiForm $form, ClientChatRequest $request): void
	{
		$cchVisitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($request->getClientRcId());
		$this->clientChatVisitorDataRepository->updateByClientChatRequest($cchVisitorData, $form->data);

		try {
			$visitorLog = $this->visitorLogRepository->findByVisitorDataId($cchVisitorData->cvd_id);
			$this->visitorLogRepository->updateByClientChatRequest($visitorLog, $form->data);
		} catch (NotFoundException $e) {
			$this->visitorLogRepository->createByClientChatRequest($cchVisitorData->cvd_id, $form->data);
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