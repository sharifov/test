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
    public const ACT_USER_SAME_EMAIL_INFO  = self::NS . 'act/user-same-email-info';
    public const ACT_CLIENT_UPDATE  = self::NS . 'act/client-update';
    public const ACT_CLIENT_SUBSCRIBE  = self::NS . 'act/client-subscribe';
    public const ACT_CLIENT_UNSUBSCRIBE  = self::NS . 'act/client-unsubscribe';
    public const ACT_SEARCH_LEADS_BY_IP  = self::NS . 'act/search-leads-by-ip';

    /** UI PERMISSION */
    public const UI_BLOCK_CLIENT_INFO  = self::NS . 'ui/block/client-info';
    public const UI_MENU_CLIENT_INFO  = self::NS . 'ui/menu/client-info';
    public const UI_FIELD_PHONE_FORM_ADD_PHONE = self::NS . 'ui/field/phone';
    public const UI_FIELD_EMAIL_FORM_ADD_EMAIL = self::NS . 'ui/field/email';
    public const UI_FIELD_LOCALE_FORM_UPDATE_CLIENT = self::NS . 'ui/field/locale';
    public const UI_FIELD_MARKETING_COUNTRY = self::NS . 'ui/field/marketing_country';

    /** LOGIC PERMISSION */
    public const LOGIC_CLIENT_DATA   = self::NS . 'logic/client_data';

    /** QUERY PERMISSIONS */
    public const QUERY_SOLD_ALL = self::NS . 'query/sold/*';
    public const QUERY_SOLD_ON_COMMON_PROJECTS = self::NS . 'query/sold/on_common_projects';
    public const QUERY_SOLD_ON_COMMON_DEPARTMENTS = self::NS . 'query/sold/on_common_departments';
    public const QUERY_SOLD_ON_COMMON_GROUPS = self::NS . 'query/sold/on_common_groups';
    public const QUERY_SOLD_IS_OWNER = self::NS . 'query/sold/is_owner';
    public const QUERY_SOLD_IS_EMPTY_OWNER = self::NS . 'query/sold/is_empty_owner';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_USER_CONVERSION   => self::ACT_USER_CONVERSION,
        self::ACT_CLIENT_DETAILS    => self::ACT_CLIENT_DETAILS,
        self::ACT_CLIENT_ADD_PHONE    => self::ACT_CLIENT_ADD_PHONE,
        self::ACT_CLIENT_EDIT_PHONE    => self::ACT_CLIENT_EDIT_PHONE,
        self::ACT_USER_SAME_PHONE_INFO    => self::ACT_USER_SAME_PHONE_INFO,
        self::ACT_CLIENT_ADD_EMAIL    => self::ACT_CLIENT_ADD_EMAIL,
        self::ACT_CLIENT_EDIT_EMAIL    => self::ACT_CLIENT_EDIT_EMAIL,
        self::ACT_USER_SAME_EMAIL_INFO    => self::ACT_USER_SAME_EMAIL_INFO,
        self::ACT_CLIENT_UPDATE    => self::ACT_CLIENT_UPDATE,
        self::ACT_CLIENT_SUBSCRIBE    => self::ACT_CLIENT_SUBSCRIBE,
        self::ACT_CLIENT_UNSUBSCRIBE    => self::ACT_CLIENT_UNSUBSCRIBE,
        self::UI_BLOCK_CLIENT_INFO  => self::UI_BLOCK_CLIENT_INFO,
        self::UI_MENU_CLIENT_INFO   => self::UI_MENU_CLIENT_INFO,
        self::ACT_SEARCH_LEADS_BY_IP   => self::ACT_SEARCH_LEADS_BY_IP,
        self::LOGIC_CLIENT_DATA   => self::LOGIC_CLIENT_DATA,
        self::UI_FIELD_PHONE_FORM_ADD_PHONE   => self::UI_FIELD_PHONE_FORM_ADD_PHONE,
        self::UI_FIELD_EMAIL_FORM_ADD_EMAIL   => self::UI_FIELD_EMAIL_FORM_ADD_EMAIL,
        self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT   => self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT,
        self::UI_FIELD_MARKETING_COUNTRY   => self::UI_FIELD_MARKETING_COUNTRY,
        self::QUERY_SOLD_ALL   => self::QUERY_SOLD_ALL,
        self::QUERY_SOLD_ON_COMMON_PROJECTS   => self::QUERY_SOLD_ON_COMMON_PROJECTS,
        self::QUERY_SOLD_ON_COMMON_DEPARTMENTS   => self::QUERY_SOLD_ON_COMMON_DEPARTMENTS,
        self::QUERY_SOLD_ON_COMMON_GROUPS   => self::QUERY_SOLD_ON_COMMON_GROUPS,
        self::QUERY_SOLD_IS_OWNER   => self::QUERY_SOLD_IS_OWNER,
        self::QUERY_SOLD_IS_EMPTY_OWNER   => self::QUERY_SOLD_IS_EMPTY_OWNER,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_CREATE  = 'create';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';
    public const ACTION_UNMASK  = 'unmask';
    public const ACTION_QUERY_AND  = 'and';
    public const ACTION_QUERY_OR  = 'or';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_USER_CONVERSION  => [self::ACTION_READ, self::ACTION_DELETE],
        self::ACT_CLIENT_DETAILS => [self::ACTION_ACCESS],
        self::ACT_CLIENT_ADD_PHONE => [self::ACTION_ACCESS, self::ACTION_CREATE],
        self::ACT_CLIENT_EDIT_PHONE => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_USER_SAME_PHONE_INFO => [self::ACTION_ACCESS],
        self::ACT_CLIENT_ADD_EMAIL => [self::ACTION_ACCESS, self::ACTION_CREATE],
        self::ACT_CLIENT_EDIT_EMAIL => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_USER_SAME_EMAIL_INFO => [self::ACTION_ACCESS],
        self::ACT_CLIENT_UPDATE => [self::ACTION_ACCESS],
        self::UI_BLOCK_CLIENT_INFO => [self::ACTION_ACCESS],
        self::UI_MENU_CLIENT_INFO => [self::ACTION_ACCESS],
        self::ACT_CLIENT_SUBSCRIBE => [self::ACTION_ACCESS],
        self::ACT_CLIENT_UNSUBSCRIBE => [self::ACTION_ACCESS],
        self::ACT_SEARCH_LEADS_BY_IP => [self::ACTION_ACCESS],
        self::LOGIC_CLIENT_DATA  => [self::ACTION_UNMASK],
        self::UI_FIELD_PHONE_FORM_ADD_PHONE  => [self::ACTION_CREATE, self::ACTION_UPDATE],
        self::UI_FIELD_EMAIL_FORM_ADD_EMAIL  => [self::ACTION_CREATE, self::ACTION_UPDATE],
        self::UI_FIELD_LOCALE_FORM_UPDATE_CLIENT  => [self::ACTION_UPDATE],
        self::UI_FIELD_MARKETING_COUNTRY  => [self::ACTION_UPDATE],
        self::QUERY_SOLD_ALL  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_ON_COMMON_PROJECTS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_ON_COMMON_DEPARTMENTS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_ON_COMMON_GROUPS  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_IS_OWNER  => [self::ACTION_ACCESS],
        self::QUERY_SOLD_IS_EMPTY_OWNER  => [self::ACTION_QUERY_AND, self::ACTION_QUERY_OR],
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

    protected const ATTR_LEAD_HAS_OWNER_QUERY = [
        'optgroup' => 'Query',
        'id' => self::NS . 'has_owner_query',
        'field' => 'has_owner_query',
        'label' => 'Condition',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['false' => 'Allow', 'true' => 'Dany'],
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
        self::ACT_USER_SAME_EMAIL_INFO    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::ACT_SEARCH_LEADS_BY_IP    => [
            self::ATTR_LEAD_IS_OWNER,
            self::ATTR_LEAD_HAS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ],
        self::LOGIC_CLIENT_DATA  => [self::ATTR_LEAD_IS_OWNER],
        self::UI_FIELD_PHONE_FORM_ADD_PHONE  => [self::ATTR_LEAD_IS_OWNER],
        self::UI_FIELD_EMAIL_FORM_ADD_EMAIL  => [self::ATTR_LEAD_IS_OWNER],
        self::QUERY_SOLD_IS_EMPTY_OWNER  => [self::ATTR_LEAD_HAS_OWNER_QUERY],
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
        $attributeList[self::ACT_USER_SAME_EMAIL_INFO][] = $attrStatus;
        $attributeList[self::ACT_SEARCH_LEADS_BY_IP][] = $attrStatus;

        return $attributeList;
    }
}
