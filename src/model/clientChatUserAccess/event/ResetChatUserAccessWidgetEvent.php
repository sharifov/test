<?php

namespace src\model\clientChatUserAccess\event;

/**
 * Class ResetChatUserAccessWidgetEvent
 * @package src\model\clientChatUserAccess\event
 *
 * @property int $userId
 */
class ResetChatUserAccessWidgetEvent
{
    public $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
}
