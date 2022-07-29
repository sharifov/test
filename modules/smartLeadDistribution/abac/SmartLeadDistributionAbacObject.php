<?php

namespace modules\smartLeadDistribution\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class SmartLeadDistributionAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'smartLeadDistribution/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** QUERY PERMISSIONS */
    public const QUERY_BUSINESS_LEAD_FIRST_CATEGORY = self::NS . 'business_leads/first_category';
    public const QUERY_BUSINESS_LEAD_SECOND_CATEGORY = self::NS . 'business_leads/second_category';
    public const QUERY_BUSINESS_LEAD_THIRD_CATEGORY = self::NS . 'business_leads/third_category';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::QUERY_BUSINESS_LEAD_FIRST_CATEGORY => self::QUERY_BUSINESS_LEAD_FIRST_CATEGORY,
        self::QUERY_BUSINESS_LEAD_SECOND_CATEGORY => self::QUERY_BUSINESS_LEAD_SECOND_CATEGORY,
        self::QUERY_BUSINESS_LEAD_THIRD_CATEGORY => self::QUERY_BUSINESS_LEAD_THIRD_CATEGORY,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::QUERY_BUSINESS_LEAD_FIRST_CATEGORY  => [self::ACTION_ACCESS],
        self::QUERY_BUSINESS_LEAD_SECOND_CATEGORY  => [self::ACTION_ACCESS],
        self::QUERY_BUSINESS_LEAD_THIRD_CATEGORY  => [self::ACTION_ACCESS],
    ];

    public const ATTR_BUSINESS_LEAD_QUANTITY_OF_FIRST_CATEGORY = [
        'optgroup' => 'Business Leads',
        'id' => self::NS . 'quantity_first_category',
        'field' => 'quantity_first_category',
        'label' => 'Quantity of first category',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, '<', '>', '<=', '>=']
    ];

    public const ATTR_BUSINESS_LEAD_QUANTITY_OF_SECOND_CATEGORY = [
        'optgroup' => 'Business Leads',
        'id' => self::NS . 'quantity_second_category',
        'field' => 'quantity_second_category',
        'label' => 'Quantity of second category',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const ATTR_BUSINESS_LEAD_QUANTITY_OF_THIRD_CATEGORY = [
        'optgroup' => 'Business Leads',
        'id' => self::NS . 'quantity_third_category',
        'field' => 'quantity_third_category',
        'label' => 'Quantity of third category',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_NUMBER,
        'values' => [],
        'multiple' => false,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::QUERY_BUSINESS_LEAD_FIRST_CATEGORY => [
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_FIRST_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_SECOND_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_THIRD_CATEGORY,
        ],
        self::QUERY_BUSINESS_LEAD_SECOND_CATEGORY => [
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_FIRST_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_SECOND_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_THIRD_CATEGORY,
        ],
        self::QUERY_BUSINESS_LEAD_THIRD_CATEGORY => [
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_FIRST_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_SECOND_CATEGORY,
            self::ATTR_BUSINESS_LEAD_QUANTITY_OF_THIRD_CATEGORY,
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

    public static function getObjectAttributeList(): array
    {
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
