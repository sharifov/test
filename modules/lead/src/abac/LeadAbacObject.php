<?php

namespace modules\lead\src\abac;

use common\models\Lead;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

/**
 * Class LeadAbacObject
 */
class LeadAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead/lead/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    /*public const ACT_ALL     = self::NS . 'act/*';
    public const ACT_CREATE  = self::NS . 'act/create';
    public const ACT_READ    = self::NS . 'act/read';
    public const ACT_UPDATE  = self::NS . 'act/update';*/
    public const ACT_USER_CONVERSION  = self::NS . 'act/user-conversion';
    public const ACT_CLIENT_DETAILS  = self::NS . 'act/client-details';
    public const ACT_CLIENT_ADD_PHONE  = self::NS . 'act/client-add-phone';
    public const ACT_CLIENT_EDIT_PHONE  = self::NS . 'act/client-edit-phone';
    public const ACT_USER_SAME_PHONE_INFO  = self::NS . 'act/user-same-phone-info';
    public const ACT_CLIENT_ADD_EMAIL  = self::NS . 'act/client-add-email';
    public const ACT_CLIENT_EDIT_EMAIL  = self::NS . 'act/client-edit-email';
    public const ACT_CLIENT_UPDATE  = self::NS . 'act/client-update';
    public const ACT_CLIENT_SUBSCRIBE  = self::NS . 'act/client-subscribe';
    public const ACT_CLIENT_UNSUBSCRIBE  = self::NS . 'act/client-unsubscribe';

    /** UI PERMISSION */
    public const UI_BLOCK_CLIENT_INFO  = self::NS . 'ui/block/client-info';
    public const UI_MENU_CLIENT_INFO  = self::NS . 'ui/menu/client-info';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_USER_CONVERSION   => self::ACT_USER_CONVERSION,
        self::ACT_CLIENT_DETAILS    => self::ACT_CLIENT_DETAILS,
        self::ACT_CLIENT_ADD_PHONE    => self::ACT_CLIENT_ADD_PHONE,
        self::ACT_CLIENT_EDIT_PHONE    => self::ACT_CLIENT_EDIT_PHONE,
        self::ACT_USER_SAME_PHONE_INFO    => self::ACT_USER_SAME_PHONE_INFO,
        self::ACT_CLIENT_ADD_EMAIL    => self::ACT_CLIENT_ADD_EMAIL,
        self::ACT_CLIENT_EDIT_EMAIL    => self::ACT_CLIENT_EDIT_EMAIL,
        self::ACT_CLIENT_UPDATE    => self::ACT_CLIENT_UPDATE,
        self::ACT_CLIENT_SUBSCRIBE    => self::ACT_CLIENT_SUBSCRIBE,
        self::ACT_CLIENT_UNSUBSCRIBE    => self::ACT_CLIENT_UNSUBSCRIBE,
        self::UI_BLOCK_CLIENT_INFO  => self::UI_BLOCK_CLIENT_INFO,
        self::UI_MENU_CLIENT_INFO   => self::UI_MENU_CLIENT_INFO
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_CREATE  = 'create';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_CONVERSION  => [self::ACTION_READ, self::ACTION_DELETE],
        self::ACT_CLIENT_DETAILS => [self::ACTION_ACCESS],
        self::ACT_CLIENT_ADD_PHONE => [self::ACTION_ACCESS],
        self::ACT_CLIENT_EDIT_PHONE => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_USER_SAME_PHONE_INFO => [self::ACTION_ACCESS],
        self::ACT_CLIENT_ADD_EMAIL => [self::ACTION_ACCESS],
        self::ACT_CLIENT_EDIT_EMAIL => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_CLIENT_UPDATE => [self::ACTION_ACCESS],
        self::UI_BLOCK_CLIENT_INFO => [self::ACTION_ACCESS],
        self::UI_MENU_CLIENT_INFO => [self::ACTION_ACCESS],
        self::ACT_CLIENT_SUBSCRIBE => [self::ACTION_ACCESS],
        self::ACT_CLIENT_UNSUBSCRIBE => [self::ACTION_ACCESS]
    ];

    protected const ATTR_LEAD_IS_OWNER = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_LEAD_HAS_OWNER = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'has_owner',
        'field' => 'has_owner',
        'label' => 'Has Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'is_common_group',
        'field' => 'is_common_group',
        'label' => 'Is Common Group',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'status_id',
        'field' => 'status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
            self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_USER_CONVERSION    => [self::ATTR_LEAD_IS_OWNER],
        self::UI_BLOCK_CLIENT_INFO   => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::UI_MENU_CLIENT_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_DETAILS    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_ADD_PHONE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_ADD_EMAIL    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_UPDATE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_SUBSCRIBE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_UNSUBSCRIBE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_EDIT_PHONE    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_USER_SAME_PHONE_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_CLIENT_EDIT_EMAIL    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
    ];

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
        $attrStatus = self::ATTR_LEAD_STATUS;
        $attrStatus['values'] = Lead::getStatusList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::UI_BLOCK_CLIENT_INFO][] = $attrStatus;
        $attributeList[self::UI_MENU_CLIENT_INFO][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_DETAILS][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_ADD_PHONE][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_ADD_EMAIL][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_UPDATE][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_SUBSCRIBE][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_UNSUBSCRIBE][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_EDIT_PHONE][] = $attrStatus;
        $attributeList[self::ACT_USER_SAME_PHONE_INFO][] = $attrStatus;
        $attributeList[self::ACT_CLIENT_EDIT_EMAIL][] = $attrStatus;

        return $attributeList;
    }
}
