<?php

namespace src\model\clientChat\entity\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class ClientChatAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'client-chat/client-chat/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_CREATE_SEND_QUOTE  = self::NS . 'act/create-send-quote';
    public const CLIENT_CHAT_FORM  = self::NS . 'client-chat-from';


    public const OBJECT_LIST = [
        self::ACT_CREATE_SEND_QUOTE => self::ACT_CREATE_SEND_QUOTE,
        self::CLIENT_CHAT_FORM   => self::CLIENT_CHAT_FORM,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_CREATE_SEND_QUOTE => [self::ACTION_CREATE],
        self::CLIENT_CHAT_FORM => [self::ACTION_ACCESS]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    /**
     * @return string[]
     */
    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    /**
     * @return \array[][]
     */
    public static function getObjectAttributeList(): array
    {
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
