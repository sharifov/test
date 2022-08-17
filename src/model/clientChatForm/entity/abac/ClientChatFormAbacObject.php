<?php

namespace src\model\clientChatForm\entity\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class ClientChatFormAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'client-chat-form/client-chat-form/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const UI_CRUD = self::NS . 'ui/crud';

    public const OBJECT_LIST = [
        self::UI_CRUD => self::UI_CRUD,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_BUILDER  = 'builder';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::UI_CRUD => [self::ACTION_DELETE, self::ACTION_UPDATE, self::ACTION_BUILDER],
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
