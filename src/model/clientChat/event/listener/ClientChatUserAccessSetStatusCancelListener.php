<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\event\ClosedStatusGroupEventInterface;
use src\services\clientChatUserAccessService\ClientChatUserAccessService;

/**
 * Class ClientChatUserAccessSetStatusCancelListener
 * @package src\model\clientChat\event
 *
 * @property ClientChatUserAccessService $accessService
 */
class ClientChatUserAccessSetStatusCancelListener
{
    /**
     * @var ClientChatUserAccessService
     */
    private ClientChatUserAccessService $accessService;

    public function __construct(ClientChatUserAccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function handle(ClosedStatusGroupEventInterface $event): void
    {
        try {
            $this->accessService->disableAccessForOtherUsersBatch($event->getChatId(), $event->getOwnerId());
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatSetStatusIdleListener'
            );
        }
    }
}
