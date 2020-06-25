<?php

namespace sales\model\clientChatRequest\useCase\api\create;

use sales\dispatchers\NativeEventDispatcher;

/**
 * Class ClientChatRequestService
 * @package sales\model\clientChatRequest\useCase\api\create
 *
 * @property ClientChatRequestRepository $clientChatRequestRepository
 */
class ClientChatRequestService
{

	/**
	 * @var ClientChatRequestRepository
	 */
	private ClientChatRequestRepository $clientChatRequestRepository;

	public function __construct(ClientChatRequestRepository $clientChatRequestRepository)
	{
		$this->clientChatRequestRepository = $clientChatRequestRepository;
	}

	/**
	 * @param ClientChatRequestApiForm $form
	 * @throws \JsonException
	 */
	public function create(ClientChatRequestApiForm $form): void
	{
		$this->clientChatRequestRepository->create($form);
		NativeEventDispatcher::triggerAll();
	}
}