<?php

namespace src\model\clientChatUserAccess\event;

use src\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;

/**
 * Class ResetChatUserAccessWidgetListener
 * @package src\model\clientChatUserAccess\event
 *
 * @property ClientChatUserAccessRepository $accessRepository
 */
class ResetChatUserAccessWidgetListener
{
    public $accessRepository;

    public function __construct(ClientChatUserAccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }

    public function handle(ResetChatUserAccessWidgetEvent $event): void
    {
        $this->accessRepository->resetChatUserAccessWidget($event->userId);
    }
}
