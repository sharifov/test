<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use common\models\Notifications;
use sales\helpers\app\AppHelper;
use sales\repositories\clientChatChannel\ClientChatChannelRepository;
use sales\model\clientChat\entity\ClientChat;
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
use common\components\CentrifugoService;
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
 * @property ClientChatChannelRepository $clientChatChannelRepository
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
	 * @var ClientChatChannelRepository
	 */
	private ClientChatChannelRepository $clientChatChannelRepository;

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
		ClientChatVisitorDataRepository $clientChatVisitorDataRepository,
		ClientChatChannelRepository $clientChatChannelRepository
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
		$this->clientChatChannelRepository = $clientChatChannelRepository;
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
				$this->roomConnected($form, $clientChatRequest);
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
		$visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($clientChatRequest->getClientRcId());

		if ($visitorData->clientChatVisitors) {
			foreach ($visitorData->clientChatVisitors as $chatVisitor) {
				$clientChat = $this->clientChatRepository->findById($chatVisitor->ccv_cch_id);
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
	 * @param ClientChatRequestApiForm $form
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function roomConnected(ClientChatRequestApiForm $form, ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest, ClientChat::SOURCE_TYPE_CLIENT);
		if (!$clientChat->cch_client_id) {
			$client = $this->clientManageService->getOrCreateByClientChatRequest($clientChatRequest, (int)$clientChat->cch_project_id);
			$clientChat->cch_client_id = $client->id;
		}
		$clientChat->cch_client_online = 1;

		if (!$clientChat->cch_channel_id) {
			$channel = $this->clientChatService->assignClientChatChannel($clientChat, $clientChatRequest->getChannelIdFromData());
			if ($channel->ccc_project_id !== $clientChat->cch_project_id) {
				throw new \DomainException('Channel project does not match project from api request');
			}
			$this->clientChatRepository->save($clientChat);
			$this->clientChatService->sendRequestToUsers($clientChat, $channel);
		} else {
			$this->clientChatRepository->save($clientChat);
		}

		$visitorRcId = $clientChatRequest->getClientRcId();
		$this->manageChatVisitorData($clientChat->cch_id, $clientChat->cch_client_id, $visitorRcId, $form);

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
		}
		$message = ClientChatMessage::createByApi($form, $clientChat, $clientChatRequest);
		$this->clientChatMessageRepository->save($message, 0);
        $this->sendLastChatMessageToMonitor($clientChat, $message);
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

    public function sendLastChatMessageToMonitor(ClientChat $clientChat, ClientChatMessage $message): void
    {
        $data = [];
        $data['chat_id'] = $message->ccm_cch_id;
        $data['client_id'] = $message->ccm_client_id;
        $data['user_id'] = $message->ccm_user_id;
        $data['sent_dt'] = \Yii::$app->formatter->asDatetime(strtotime($message->ccm_sent_dt), 'php: Y-m-d H:i:s');
        $data['period'] = \Yii::$app->formatter->asRelativeTime(strtotime($message->ccm_sent_dt));
        $data['msg'] = $message->message;
        CentrifugoService::sendMsg(json_encode([
            'chatMessageData' => $data,
        ]), 'realtimeClientChatChannel');
    }

	/**
	 * @param int $chatId
	 * @param int $clientId
	 * @param string $visitorRcId
	 * @param ClientChatRequestApiForm $form
	 */
    private function manageChatVisitorData(int $chatId, int $clientId, string $visitorRcId, ClientChatRequestApiForm $form): void
	{
		try {
			$visitorData = $this->clientChatVisitorDataRepository->findByVisitorRcId($visitorRcId);
			$visitorData->updateByClientChatRequest($form->data);
			$this->clientChatVisitorDataRepository->save($visitorData);
			if (!$this->clientChatVisitorRepository->exists($chatId, $visitorData->cvd_id)) {
				$this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
			}
		} catch (NotFoundException $e) {
			$visitorData = $this->clientChatVisitorDataRepository->createByClientChatRequest($visitorRcId, $form->data);
			$this->clientChatVisitorRepository->create($chatId, $visitorData->cvd_id, $clientId);
		}

		try {
			$visitorLog = $this->visitorLogRepository->findByVisitorDataId($visitorData->cvd_id);
			$visitorLog->updateByClientChatRequest($form->data);
			$this->visitorLogRepository->save($visitorLog);
		} catch (NotFoundException $e) {
			$this->visitorLogRepository->createByClientChatRequest($visitorData->cvd_id, $form->data);
		}
	}
}