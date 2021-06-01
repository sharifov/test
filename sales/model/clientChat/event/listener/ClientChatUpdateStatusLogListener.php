<?php

namespace sales\model\clientChat\event\listener;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\event\ClientChatUpdateStatusEvent;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatUpdateStatusLogListener
 * @package sales\model\clientChat\event\listener
 *
 * @property-read ClientChatStatusLogService $chatStatusLogService
 * @property-read ClientChatRepository $clientChatRepository
 */
class ClientChatUpdateStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;

    public function __construct(ClientChatStatusLogService $chatStatusLogService, ClientChatRepository $clientChatRepository)
    {
        $this->chatStatusLogService = $chatStatusLogService;
        $this->clientChatRepository = $clientChatRepository;
    }

    public function handle(ClientChatUpdateStatusEvent $event): void
    {
        try {
            $chat = $this->clientChatRepository->findById($event->chatId);

            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatusId,
                $event->newStatusId,
                $chat->cch_owner_user_id,
                null,
                $event->userId,
                $event->prevChannelId,
                $event->actionType,
                null,
                $chat->cch_rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatUpdateStatusLogListener');
        }
    }
}
