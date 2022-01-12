<?php

namespace src\model\clientChat\event;

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

    public function handle(ClientChatCloseEvent $event): void
    {
        try {
            $this->accessService->disableAccessForOtherUsersBatch($event->clientChatId, $event->chatOwnerId);
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatSetStatusIdleListener'
            );
        }
    }
}
