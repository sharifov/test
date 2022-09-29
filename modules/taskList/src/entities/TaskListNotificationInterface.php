<?php

namespace modules\taskList\src\entities;

interface TaskListNotificationInterface
{
    /**
     * @return self
     */
    public function send(): self;

    /**
     * @return string
     */
    public static function getType(): string;
}
