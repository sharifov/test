<?php

namespace modules\lead\src\abac;

use common\models\Lead;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use yii\helpers\ArrayHelper;

class LeadExpertCallObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'lead/expert_call/';

    /** ACTION PERMISSION */
    public const ACT_CALL = self::NS . 'act/call';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_CALL => self::ACT_CALL,
    ];

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_CALL => [self::ACTION_ACCESS],
    ];

    public const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadStatus',
        'field' => 'leadStatus',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => false,
        'operators' => [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_HAS_FLIGHT_SEGMENT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'hasFlightSegment',
        'field' => 'hasFlightSegment',
        'label' => 'Has Flight Segment',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' => [self::OP_EQUAL2]
    ];

    public const ATTR_QUOTE_COUNT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'quoteCount',
        'field' => 'quoteCount',
        'label' => 'Amount SS or Auto Quote in Status: Send,Decline,Opened',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];


    public const ATTR_CALL_COUNT = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'callCount',
        'field' => 'callCount',
        'label' => 'Call amount',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_TEXT,
        'values' => [],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];

    protected const ATTR_CAN_MAKE_CALL = [
        'optgroup' => 'User',
        'id' => self::NS . 'canMakeCall',
        'field' => 'canMakeCall',
        'label' => 'Can Make Call',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        'operators' => [self::OP_EQUAL2]
    ];

    protected const ATTR_LEAD_TYPE_ID = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'leadTypeId',
        'field' => 'leadTypeId',
        'label' => 'Type',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_IN, self::OP_NOT_IN]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_CALL   => [
            self::ATTR_HAS_FLIGHT_SEGMENT,
            self::ATTR_QUOTE_COUNT,
            self::ATTR_CALL_COUNT,
            self::ATTR_CAN_MAKE_CALL
        ],
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
        $leadStatuses = Lead::getAllStatuses();
        $attrStatus = self::ATTR_LEAD_STATUS;
        $attrStatus['values'] = $leadStatuses;

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::ACT_CALL][] = $attrStatus;

        $attrLeadType = self::ATTR_LEAD_TYPE_ID;
        $attrLeadType['values'] = ArrayHelper::merge(Lead::TYPE_LIST, [Lead::TYPE_BASIC => 'Basic']);
        $attributeList[self::ACT_CALL][] = $attrLeadType;

        return $attributeList;
    }
}
