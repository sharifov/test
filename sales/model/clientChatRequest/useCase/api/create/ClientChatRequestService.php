<?php

namespace sales\model\clientChatRequest\useCase\api\create;

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
	 * ClientChatRequestService constructor.
	 * @param ClientChatRequestRepository $clientChatRequestRepository
	 * @param ClientChatRepository $clientChatRepository
	 * @param ClientManageService $clientManageService
	 * @param ClientChatMessageRepository $clientChatMessageRepository
	 * @param ClientChatMessageService $clientChatMessageService
	 * @param ClientChatService $clientChatService
	 * @param VisitorLogRepository $visitorLogRepository
	 * @param ClientChatDataRepository $clientChatDataRepository
	 */
	public function __construct(
		ClientChatRequestRepository $clientChatRequestRepository,
		ClientChatRepository $clientChatRepository,
		ClientManageService $clientManageService,
		ClientChatMessageRepository $clientChatMessageRepository,
		ClientChatMessageService $clientChatMessageService,
		ClientChatService $clientChatService,
		VisitorLogRepository $visitorLogRepository,
		ClientChatDataRepository $clientChatDataRepository
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
		$this->saveAdditionalData($clientChat, $form);
	}

	/**
	 * @param ClientChatRequest $clientChatRequest
	 */
	private function roomConnected(ClientChatRequest $clientChatRequest): void
	{
		$clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest);
		$this->clientChatService->assignToChannel($clientChat);
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
		}
	}

	private function saveAdditionalData(ClientChat $clientChat, ClientChatRequestApiForm $form): void
	{
		$this->visitorLogRepository->createByClientChatRequest($clientChat, $form->data);

		if (!$this->clientChatDataRepository->exist($clientChat->cch_id)) {
			$this->clientChatDataRepository->createByClientChatRequest($clientChat, $form->data);
		}
	}
}