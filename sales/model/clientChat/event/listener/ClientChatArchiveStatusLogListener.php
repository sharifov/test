<?php

namespace sales\model\clientChat\event\listener;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\event\ClientChatArchiveEvent;
use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatArchiveStatusLogListener
 * @package sales\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatArchiveStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatArchiveEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatus,
                ClientChat::STATUS_ARCHIVE,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatArchiveStatusLogListener');
        }
    }
}
