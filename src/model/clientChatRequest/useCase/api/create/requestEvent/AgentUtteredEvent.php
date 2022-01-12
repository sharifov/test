<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatMessage\ClientChatMessageRepository;
use src\model\clientChatMessage\entity\ClientChatMessage;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatService\ClientChatService;
use src\services\TransactionManager;

/**
 * Class AgentUtteredEvent
 * @package src\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatMessageRepository $clientChatMessageRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatRequestApiForm $form
 * @property TransactionManager $transactionManager
 */
class AgentUtteredEvent implements ChatRequestEvent
{
    /**
     * @var ClientChatMessageRepository
     */
    private ClientChatMessageRepository $clientChatMessageRepository;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ClientChatRequestApiForm $form
     */
    public ClientChatRequestApiForm $form;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    public function __construct(
        ClientChatMessageRepository $clientChatMessageRepository,
        ClientChatMessageService $clientChatMessageService,
        ClientChatRepository $clientChatRepository,
        ClientChatService $clientChatService,
        TransactionManager $transactionManager
    ) {
        $this->clientChatMessageRepository = $clientChatMessageRepository;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatService = $clientChatService;
        $this->transactionManager = $transactionManager;
    }

    public function process(ClientChatRequest $request): void
    {
        $this->transactionManager->wrap(function () use ($request) {
            $message = ClientChatMessage::createByApi($this->form, $request->ccr_event, $request->getPlatformId());
            $this->clientChatMessageRepository->save($message, 0);

            $clientChat = $this->clientChatRepository->getLastByRid($this->form->data['rid'] ?? '');
            if ($clientChat) {
                $this->clientChatMessageService->assignMessageToChat($message, $clientChat);
                if ($clientChat->isIdle()) {
                    $this->clientChatService->autoReturn($clientChat);
                }
            }
        });
    }

    public function getClassName(): string
    {
        return self::class;
    }
}
