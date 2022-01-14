<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatNewEvent;
use src\services\clientChatService\ClientChatStatusLogService;

class ClientChatNewStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatNewEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chat->cch_id,
                $event->oldStatus,
                ClientChat::STATUS_NEW,
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
