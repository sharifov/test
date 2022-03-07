<?php

namespace src\model\quote\abac;

use common\models\Lead;
use common\models\Quote;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class QuoteFlightAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'quote/quote/';

    public const OBJ_EXTRA_MARKUP =  self::NS . 'obj-extra-markup';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_UPDATE  = 'update';


    public const OBJECT_LIST = [
        self::OBJ_EXTRA_MARKUP => self::ACTION_UPDATE,
    ];

    public const OBJECT_ACTION_LIST = [
        self::OBJ_EXTRA_MARKUP => [ self::ACTION_UPDATE ],
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_EXTRA_MARKUP => [
        ],
    ];

    protected const ATTR_LEAD_STATUS = [
        'optgroup' => 'Lead',
        'id' => self::NS . 'lead_status_id',
        'field' => 'lead_status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                         self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_QUOTE_STATUS = [
        'optgroup' => 'Quote',
        'id' => self::NS . 'quote_status_id',
        'field' => 'quote_status_id',
        'label' => 'Status',
        'type' => self::ATTR_TYPE_INTEGER,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [],
        'multiple' => true,
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                         self::OP_IN, self::OP_NOT_IN]
    ];

    protected const ATTR_USER_IS_OWNER = [
        'optgroup' => 'User',
        'id' => self::NS . 'is_owner',
        'field' => 'is_owner',
        'label' => 'Is owner',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_SELECT,
        'values' => [ 0 => false, 1 => true],
        'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                         self::OP_IN, self::OP_NOT_IN],
        'multiple' => false,
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
        $attrLeadStatus                                    = self::ATTR_LEAD_STATUS;
        $attrLeadStatus['values']                          = Lead::getAllStatuses();
        $attrQuoteStatus                                   = self::ATTR_QUOTE_STATUS;
        $attrQuoteStatus['values']                         = Quote::STATUS_LIST;
        $attributeList                                     = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::OBJ_EXTRA_MARKUP][] = $attrLeadStatus;
        $attributeList[self::OBJ_EXTRA_MARKUP][] = $attrQuoteStatus;
        $attrIsOwner                                       = self::ATTR_USER_IS_OWNER;
        $attributeList[self::OBJ_EXTRA_MARKUP][] = $attrIsOwner;
        return $attributeList;
    }
}
