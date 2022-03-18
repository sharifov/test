<?php

namespace modules\notification\src\abac;

use common\models\Notifications;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class NotificationAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'notification/notification/';

    /** OBJECT PERMISSION */
    public const OBJ_NOTIFICATION = self::NS . 'obj/notification';
    public const OBJ_NOTIFICATION_MULTIPLE_UPDATE = self::NS . 'obj/notification/multiple-update';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::OBJ_NOTIFICATION => self::OBJ_NOTIFICATION,
        self::OBJ_NOTIFICATION_MULTIPLE_UPDATE => self::OBJ_NOTIFICATION_MULTIPLE_UPDATE,
    ];

    /** --------------- ACTIONS ------------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_MULTIPLE_UPDATE_MAKE_READ = 'multipleUpdateMakeRead';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJ_NOTIFICATION => [self::ACTION_ACCESS],
        self::OBJ_NOTIFICATION_MULTIPLE_UPDATE => [self::ACTION_MULTIPLE_UPDATE_MAKE_READ],
    ];

    protected const ATTR_NOTIFICATION_TYPE = [
        'optgroup' => 'NOTIFICATION',
        'id' => self::NS . 'type',
        'field' => 'type',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_NOTIFICATION_TITLE = [
        'optgroup' => 'NOTIFICATION',
        'id' => self::NS . 'title',
        'field' => 'title',
        'label' => 'Title',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_TEXT,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN, self::OP_MATCH]
    ];

    protected const ATTR_NOTIFICATION_USER_ID = [
        'optgroup' => 'NOTIFICATION',
        'id' => self::NS . 'userId',
        'field' => 'userId',
        'label' => 'User ID',
        'type' => self::ATTR_TYPE_INTEGER,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    /** --------------- ATTRIBUTE LIST ------------------------ */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_NOTIFICATION  =>  [
            self::ATTR_NOTIFICATION_TITLE,
            self::ATTR_NOTIFICATION_USER_ID
        ],
        self::OBJ_NOTIFICATION_MULTIPLE_UPDATE => [
            self::ATTR_NOTIFICATION_TITLE,
            self::ATTR_NOTIFICATION_USER_ID
        ]
    ];

    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

    public static function getObjectActionList(): array
    {
        return self::OBJECT_ACTION_LIST;
    }

    public static function getObjectAttributeList(): array
    {
        $attrTypeList = self::ATTR_NOTIFICATION_TYPE;
        $attrTypeList['values'] = Notifications::getTypeList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::OBJ_NOTIFICATION][] = $attrTypeList;
        $attributeList[self::OBJ_NOTIFICATION_MULTIPLE_UPDATE][] = $attrTypeList;

        return $attributeList;
    }
}
