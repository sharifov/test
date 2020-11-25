<?php

namespace sales\model\clientChat\event\listener;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\event\ClientChatCloseEvent;
use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatCloseStatusLogListener
 * @package sales\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatCloseStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatCloseEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatus,
                ClientChat::STATUS_CLOSED,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatCloseStatusLogListener');
        }
    }
}
