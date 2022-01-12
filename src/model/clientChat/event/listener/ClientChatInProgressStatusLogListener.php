<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatInProgressEvent;
use src\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatInProgressStatusLogListener
 * @package src\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatInProgressStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatInProgressEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chat->cch_id,
                $event->oldStatus,
                ClientChat::STATUS_IN_PROGRESS,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatInProgressStatusLogListener');
        }
    }
}
