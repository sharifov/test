<?php

namespace sales\model\clientChat\event;

use sales\services\clientChatMessage\ClientChatMessageService;

/**
 * Class ClientChatRemoveOldOwnerUnreadMessagesListener
 *
 * @property ClientChatMessageService $service
 */
class ClientChatRemoveOldOwnerUnreadMessagesListener
{
    private ClientChatMessageService $service;

    public function __construct(ClientChatMessageService $service)
    {
        $this->service = $service;
    }

    public function handle(ClientChatOwnerAssignedEvent $event): void
    {
        $this->service->discardUnreadMessages($event->chat->cch_id, $event->oldOwner);
    }
}
