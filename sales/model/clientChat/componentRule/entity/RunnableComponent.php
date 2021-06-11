<?php

namespace sales\model\clientChat\componentRule\entity;

class RunnableComponent
{
    private const SEND_MESSAGE_SUBSCRIBE = 1;

    private const LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => 'SendMessageSubscribe'
    ];

    public static function getListName(): array
    {
        return self::LIST_NAME;
    }
}
