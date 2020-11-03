<?php


namespace sales\model\clientChat\event\listener;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\event\ClientChatInProgressEvent;
use sales\services\clientChatService\ClientChatStatusLogService;

/**
 * Class ClientChatInProgressStatusLogListener
 * @package sales\model\clientChat\event
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
                $event->reasonId
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'ClientChatListener:ClientChatInProgressStatusLogListener');
        }
    }
}
