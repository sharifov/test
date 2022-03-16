<?php

namespace modules\user\userFeedback\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class UserFeedbackAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'user-feedback/';

    public const OBJ_USER_FEEDBACK = self::NS . 'obj/user-feedback';
    public const ACT_USER_FEEDBACK_INDEX = self::NS . 'act/user-feedback-index';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';
    public const ACTION_CREATE = 'create';
    public const ACTION_READ   = 'read';

    public const OBJECT_LIST = [
        self::OBJ_USER_FEEDBACK => self::OBJ_USER_FEEDBACK,
        self::ACT_USER_FEEDBACK_INDEX => self::ACT_USER_FEEDBACK_INDEX,
    ];

    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_FEEDBACK_INDEX => [self::ACTION_ACCESS],
        self::OBJ_USER_FEEDBACK       => [self::ACTION_CREATE, self::ACTION_READ],
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_USER_FEEDBACK_INDEX               => [
        ],
        self::OBJ_USER_FEEDBACK => [
        ],
    ];

    protected const ATTR_USER_IS_OWNER = [
        'optgroup'  => 'User',
        'id'        => self::NS . 'is_owner',
        'field'     => 'is_owner',
        'label'     => 'Is owner',
        'type'      => self::ATTR_TYPE_BOOLEAN,
        'input'     => self::ATTR_INPUT_SELECT,
        'values'    => [0 => false, 1 => true],
        'operators' => [
            self::OP_EQUAL2,
            self::OP_NOT_EQUAL2,
            self::OP_IN,
            self::OP_NOT_IN
        ],
        'multiple'  => false,
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
        $attributeList                   = self::OBJECT_ATTRIBUTE_LIST;
        $attrIsOwner                     = self::ATTR_USER_IS_OWNER;
        $attributeList[self::OBJ_USER_FEEDBACK][] = $attrIsOwner;
        return $attributeList;
    }
}
