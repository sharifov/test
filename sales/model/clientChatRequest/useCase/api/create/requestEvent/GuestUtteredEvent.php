<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEvent;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\cloneChat\ClientChatCloneDto;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;

/**
 * Class GuestUtteredEvent
 * @package sales\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatMessageRepository $clientChatMessageRepository
 * @property ClientChatMessageService $clientChatMessageService
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 * @property ClientChatRequestApiForm $form
 * @property TransactionManager $transactionManager
 */
class GuestUtteredEvent implements ChatRequestEvent
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
            $message = ClientChatMessage::createByApi($this->form, $request->ccr_event);
            $this->clientChatMessageRepository->save($message, 0);

            $clientChat = $this->clientChatRepository->getLastByRid($this->form->data['rid'] ?? '');
            if ($clientChat) {
                $this->clientChatMessageService->assignMessageToChat($message, $clientChat);

                if ($clientChat->isClosed() && $owner = $clientChat->cchOwnerUser) {
                    if ($owner->isOnline()) {
                        $this->clientChatService->autoReopen($clientChat);
                    } else {
                        $clientChat->archive(null, ClientChatStatusLog::ACTION_AUTO_CLOSE, null, null);
                        $dto = ClientChatCloneDto::feelInOnClone($clientChat);
                        $this->clientChatRepository->save($clientChat);

                        $newClientChat = ClientChat::clone($dto);
                        $newClientChat->cch_source_type_id = ClientChat::SOURCE_TYPE_GUEST_UTTERED;
                        $newClientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
                        $this->clientChatRepository->save($newClientChat);

                        $this->clientChatService->cloneLead($clientChat, $newClientChat)
                            ->cloneCase($clientChat, $newClientChat)
                            ->cloneNotes($clientChat, $newClientChat);

                        $this->clientChatService->sendRequestToUsers($newClientChat);
                    }
                }
            }
        });
    }

    public function getClassName(): string
    {
        return self::class;
    }
}
