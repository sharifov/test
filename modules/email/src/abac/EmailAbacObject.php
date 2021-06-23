<?php

namespace modules\email\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class EmailAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'email/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL     = self::NS . 'act/*';
    public const ACT_VIEW  = self::NS . 'act/view';

    public const OBJECT_LIST = [
        self::ACT_VIEW => self::ACT_VIEW,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_VIEW  => [self::ACTION_ACCESS],
    ];

    protected const ATTR_IS_EMAIL_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_email_owner',
        'field' => 'is_email_owner',
        'label' => 'Is Email Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_HAS_CREATOR = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'has_creator',
        'field' => 'has_creator',
        'label' => 'Has Creator',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_CASE_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_case_owner',
        'field' => 'is_case_owner',
        'label' => 'Is Case Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_LEAD_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_lead_owner',
        'field' => 'is_lead_owner',
        'label' => 'Is Lead Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_ADDRESS_OWNER = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_address_owner',
        'field' => 'is_address_owner',
        'label' => 'Is Address Owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    protected const ATTR_IS_COMMON_GROUP = [
        'optgroup' => 'EMAIL',
        'id' => self::NS . 'is_common_group',
        'field' => 'is_common_group',
        'label' => 'Is Common Group',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'Yes', 'false' => 'No'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_VIEW => [
            self::ATTR_IS_EMAIL_OWNER,
            self::ATTR_HAS_CREATOR,
            self::ATTR_IS_CASE_OWNER,
            self::ATTR_IS_LEAD_OWNER,
            self::ATTR_IS_ADDRESS_OWNER,
            self::ATTR_IS_COMMON_GROUP
        ]
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

        //$attributeList = self::OBJECT_ATTRIBUTE_LIST;
       // $attributeList[self::ACT_VIEW][] = self::OBJECT_ATTRIBUTE_LIST;

        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
