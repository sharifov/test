<?php

namespace sales\model\clientChat\event;

/**
 * Class ClientChatOwnerAssignedEvent
 *
 * @property int $chatId
 * @property int|null $oldOwner
 * @property int|null $newOwner
 */
class ClientChatOwnerAssignedEvent
{
    public int $chatId;
    public ?int $oldOwner;
    public ?int $newOwner;

    public function __construct(int $chatId, ?int $oldOwner, ?int $newOwner)
    {
        $this->chatId = $chatId;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
    }
}
