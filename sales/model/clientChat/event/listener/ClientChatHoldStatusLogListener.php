<?php

namespace sales\model\clientChat\event\listener;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\event\ClientChatHoldEvent;
use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatHoldStatusLogListener
 * @package sales\model\clientChat\event
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
