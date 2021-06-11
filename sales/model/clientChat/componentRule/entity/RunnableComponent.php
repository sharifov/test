<?php

namespace sales\model\clientChat\componentRule\entity;

use sales\model\clientChat\componentRule\component\SendMessageToSubscriber;

class RunnableComponent
{
    private const SEND_MESSAGE_SUBSCRIBE = 1;

    private const LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => 'SendMessageSubscribe'
    ];

    private const CLASS_LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => SendMessageToSubscriber::class
    ];

    public static function getListName(): array
    {
        return self::LIST_NAME;
    }

    public static function getClassListName(): array
    {
        return self::CLASS_LIST_NAME;
    }
}
