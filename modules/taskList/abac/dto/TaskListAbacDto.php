<?php

namespace modules\taskList\abac\dto;

class TaskListAbacDto extends \stdClass
{
    public bool $isUserTaskOwner = false;

    public function setIsUserTaskOwner(bool $isOwner): void
    {
        $this->isUserTaskOwner = $isOwner;
    }
}
