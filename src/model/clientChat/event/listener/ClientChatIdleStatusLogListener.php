<?php

namespace src\model\clientChat\event\listener;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClientChatIdleEvent;
use src\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatIdleStatusLogListener
 * @package src\model\clientChat\event
 *
 * @property ClientChatStatusLogService $chatStatusLogService
 */
class ClientChatIdleStatusLogListener
{
    /**
     * @var ClientChatStatusLogService
     */
    private ClientChatStatusLogService $chatStatusLogService;

    public function __construct(ClientChatStatusLogService $chatStatusLogService)
    {
        $this->chatStatusLogService = $chatStatusLogService;
    }

    public function handle(ClientChatIdleEvent $event): void
    {
        try {
            $this->chatStatusLogService->log(
                $event->chatId,
                $event->oldStatus,
                ClientChat::STATUS_IDLE,
                $event->ownerId,
                $event->description,
                $event->creatorUserId,
                $event->prevChannelId,
                $event->actionType,
                $event->reasonId,
                $event->rid
            );
            Notifications::pub(
                ['chat-' . $event->chatId],
                'reloadChatInfo',
                ['data' => ClientChatAccessMessage::chatIdle($event->chatId)]
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatIdleStatusLogListener');
        }
    }
}
