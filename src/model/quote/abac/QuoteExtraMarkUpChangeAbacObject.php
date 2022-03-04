<?php

namespace src\model\quote\abac;

use common\models\Lead;
use common\models\Quote;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class QuoteExtraMarkUpChangeAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'lead-view/';

    public const QUOTE_CHANGE_EXTRA_MARK_UP_FORM =  self::NS . 'action-ajax-edit-lead-quote-extra-mark-up-modal-content';

    public const ACTION_EDIT  = 'edit';

    public const OBJECT_LIST = [
        self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM => self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM,
    ];

    public const OBJECT_ACTION_LIST = [
        self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM => [ self::ACTION_EDIT],
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM => [
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
        'id' => self::NS . 'quote_status__id',
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
        $attrLeadStatus                                         = self::ATTR_LEAD_STATUS;
        $attrLeadStatus['values']                               = Lead::getAllStatuses();
        $attrQuoteStatus                                        = self::ATTR_QUOTE_STATUS;
        $attrQuoteStatus['values']                              = Quote::STATUS_LIST;
        $attributeList                                          = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM][] = $attrLeadStatus;
        $attributeList[self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM][] = $attrQuoteStatus;
        $attrIsOwner                                            = self::ATTR_USER_IS_OWNER;
        $attributeList[self::QUOTE_CHANGE_EXTRA_MARK_UP_FORM][] = $attrIsOwner;
        return $attributeList;
    }
}
