<?php

namespace sales\model\clientChatUserAccess\event;

/**
 * Class ResetChatUserAccessWidgetEvent
 * @package sales\model\clientChatUserAccess\event
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
