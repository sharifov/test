<?php

namespace modules\taskList\src\entities;

interface TaskListNotificationInterface
{
    public const NOTIFY_TYPE = '';

    /**
     * @return bool
     */
    public function send(): bool;
}
