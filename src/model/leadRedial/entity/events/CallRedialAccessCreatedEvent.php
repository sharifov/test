<?php

namespace src\model\leadRedial\entity\events;

/**
 * Class CallRedialAccessCreatedEvent
 *
 * @property int $leadId
 * @property int $userId
 */
class CallRedialAccessCreatedEvent
{
    public int $leadId;
    public int $userId;

    public function __construct(int $leadId, int $userId)
    {
        $this->leadId = $leadId;
        $this->userId = $userId;
    }
}
