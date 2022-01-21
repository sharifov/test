<?php

namespace src\model\clientChat\componentRule\entity;

use src\model\clientChat\componentRule\component\ChatDistributionLogicComponent;
use src\model\clientChat\componentRule\component\CreateLeadOnRoomConnected;
use src\model\clientChat\componentRule\component\SaveFlightQuoteSearchData;
use src\model\clientChat\componentRule\component\SendMessageToSubscriber;

class RunnableComponent
{
    private const SEND_MESSAGE_SUBSCRIBE = 1;
    public const CHAT_DISTRIBUTION_LOGIC = 2;
    public const SAVE_CHAT_DATA_REQUEST = 3;
    public const CREATE_LEAD_ON_ROOM_CONNECTED = 4;

    private const LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => 'Send Message Subscribe',
        self::CHAT_DISTRIBUTION_LOGIC => 'Chat Distribution Logic',
        self::SAVE_CHAT_DATA_REQUEST => 'Save Chat Data Request',
        self::CREATE_LEAD_ON_ROOM_CONNECTED => 'Create Lead On Room Connected'
    ];

    private const CLASS_LIST_NAME = [
        self::SEND_MESSAGE_SUBSCRIBE => SendMessageToSubscriber::class,
        self::CHAT_DISTRIBUTION_LOGIC => ChatDistributionLogicComponent::class,
        self::SAVE_CHAT_DATA_REQUEST => SaveFlightQuoteSearchData::class,
        self::CREATE_LEAD_ON_ROOM_CONNECTED => CreateLeadOnRoomConnected::class
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
