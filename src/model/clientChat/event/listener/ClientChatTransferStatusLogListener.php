<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatTransferEvent;
use src\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatTransferStatusLogListener
 * @package src\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatTransferStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatTransferEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatus,
                ClientChat::STATUS_TRANSFER,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatTransferStatusLogListener');
        }
    }
}
