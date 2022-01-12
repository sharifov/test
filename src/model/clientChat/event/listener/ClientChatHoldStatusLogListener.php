<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatHoldEvent;
use src\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatHoldStatusLogListener
 * @package src\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatHoldStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatHoldEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatus,
                ClientChat::STATUS_HOLD,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatHoldStatusLogListener');
        }
    }
}
