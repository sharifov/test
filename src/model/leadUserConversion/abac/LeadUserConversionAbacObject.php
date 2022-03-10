<?php

namespace src\model\leadUserConversion\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use src\model\leadStatusReason\entity\LeadStatusReasonQuery;

/**
 * Class LeadUserConversionAbacObject
 */
class LeadUserConversionAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead/lead/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** OBJECT PERMISSION */
    // public const OBJ_ALL     = self::NS . '*';
    public const OBJ_USER_CONVERSION = self::NS . 'obj/user-conversion';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::OBJ_USER_CONVERSION => self::OBJ_USER_CONVERSION,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_CREATE = 'create';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::OBJ_USER_CONVERSION  => [self::ACTION_CREATE],
    ];

    protected const ATTR_LEAD_IS_OWNER = [
        'optgroup' => 'LEAD',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is Owner',

        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2]
    ];

    public const ATTR_CLOSE_REASON = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'close_reason',
        'field' => 'closeReason',
        'label' => 'Lead Close Reason Key',
        'type' => self::ATTR_TYPE_STRING,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_CONTAINS]
    ];

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
        $attributeList = self::OBJECT_ATTRIBUTE_LIST;

        $attrLeadCloseReasons = self::ATTR_CLOSE_REASON;
        $closeReasons = LeadStatusReasonQuery::getList();
        $attrLeadCloseReasons['values'] = $closeReasons;
        $attributeList[self::OBJ_USER_CONVERSION][] = $attrLeadCloseReasons;

        return $attributeList;
    }
}
