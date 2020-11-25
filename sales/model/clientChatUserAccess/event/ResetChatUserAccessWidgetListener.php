<?php

namespace sales\model\clientChatUserAccess\event;

use sales\repositories\clientChatUserAccessRepository\ClientChatUserAccessRepository;

/**
 * Class ResetChatUserAccessWidgetListener
 * @package sales\model\clientChatUserAccess\event
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
