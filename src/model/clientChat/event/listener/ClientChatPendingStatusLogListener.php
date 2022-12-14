<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatPendingEvent;
use src\services\clientChatService\ClientChatStatusLogService;

class ClientChatPendingStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatPendingEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chat->cch_id,
                $event->oldStatus,
                ClientChat::STATUS_PENDING,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatPendingStatusLogListener');
        }
    }
}
