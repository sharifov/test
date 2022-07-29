<?php

namespace modules\taskList\src\events;

use modules\taskList\src\entities\userTask\UserTask;

class UserTaskStatusChangedEvent
{
    public UserTask $userTask;
    public int $newStatusId;
    public ?int $oldStatusId;
    public ?string $description;

    public function __construct(UserTask $userTask, int $newStatusId, ?int $oldStatusId = null, ?string $description = null)
    {
        $this->userTask = $userTask;
        $this->newStatusId = $newStatusId;
        $this->oldStatusId = $oldStatusId;
        $this->description = $description;
    }
}
