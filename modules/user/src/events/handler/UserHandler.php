<?php

namespace modules\user\src\events\handler;

class UserHandler
{
    public const LOGIN = 'login';

    public function login(?array $eventData = [], ?array $eventParams = [], ?array $handlerParams = []): void
    {
    }
}
