<?php

namespace modules\product\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class ProductQuoteAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'product-quote/product-quote/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_DECLINE_REPROTECTION_QUOTE = self::NS . 'act/reprotection_quote/decline';
    public const ACT_VIEW_DETAILS_REFUND_QUOTE = self::NS . 'act/refund_quote/details';

    public const OBJECT_LIST = [
        self::ACT_DECLINE_REPROTECTION_QUOTE => self::ACT_DECLINE_REPROTECTION_QUOTE,
        self::ACT_VIEW_DETAILS_REFUND_QUOTE => self::ACT_VIEW_DETAILS_REFUND_QUOTE
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_READ  = 'read';
    public const ACTION_CREATE  = 'create';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_DECLINE_REPROTECTION_QUOTE => [self::ACTION_ACCESS],
        self::ACT_VIEW_DETAILS_REFUND_QUOTE => [self::ACTION_ACCESS],
    ];

    protected const ATTR_REPROTECTION_QUOTE_IS_NEW = [
        'optgroup' => 'PRODUCT QUOTE',
        'id' => self::NS . 'is_new',
        'field' => 'is_new',
        'label' => 'Is New',
        'type' => self::ATTR_TYPE_BOOLEAN,
        'input' => self::ATTR_INPUT_RADIO,
        'values' => ['true' => 'True', 'false' => 'False'],
        'multiple' => false,
        //'validation' => ['allow_empty_value' => true],
        'operators' =>  [self::OP_EQUAL2]
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_DECLINE_REPROTECTION_QUOTE    => [self::ATTR_REPROTECTION_QUOTE_IS_NEW],
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
