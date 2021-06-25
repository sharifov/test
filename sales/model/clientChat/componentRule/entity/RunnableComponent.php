<?php

namespace sales\model\clientChat\componentRule\entity;

use sales\model\clientChat\componentRule\component\ChatDistributionLogicComponent;
use sales\model\clientChat\componentRule\component\SendMessageToSubscriber;

class RunnableComponent
{
    private const SEND_MESSAGE_SUBSCRIBE = 1;
    public const CHAT_DISTRIBUTION_LOGIC = 2;

    private const LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => 'SendMessageSubscribe',
        self::CHAT_DISTRIBUTION_LOGIC => 'ChatDistributionLogic'
    ];

    private const CLASS_LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => SendMessageToSubscriber::class,
        self::CHAT_DISTRIBUTION_LOGIC => ChatDistributionLogicComponent::class
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
