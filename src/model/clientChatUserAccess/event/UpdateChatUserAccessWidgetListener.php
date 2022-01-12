<?php

namespace src\model\clientChatUserAccess\event;

use src\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;

/**
 * Class UpdateChatUserAccessWidgetListener
 * @package src\model\clientChatUserAccess\event
 *
 * @property ClientChatUserAccessRepository $accessRepository
 */
class UpdateChatUserAccessWidgetListener
{
    public $accessRepository;

    public function __construct(ClientChatUserAccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }

    public function handle(UpdateChatUserAccessWidgetEvent $event): void
    {
        $this->accessRepository->updateChatUserAccessWidget($event->chat->cch_id, $event->userId, $event->statusId, $event->ccuaId);
    }
}
