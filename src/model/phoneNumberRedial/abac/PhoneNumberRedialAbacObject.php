<?php

namespace modules\order\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;

class PhoneNumberRedialAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'order/order/';

    /** --------------- PERMISSIONS --------------------------- */


    /** ACTION PERMISSION */
    public const ACT_DELETE  = self::NS . 'act/delete';

    /** UI PERMISSION */
    public const UI_BTN_CREATE      = self::NS . 'ui/btn/create';



    /** OBJECT PERMISSION */
    public const OBJ_ALL            = self::NS . 'obj/*';



    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_DELETE    => self::ACT_DELETE,
        self::UI_BTN_CREATE     => self::UI_BTN_CREATE,

        self::OBJ_ALL       => self::OBJ_ALL,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_DELETE    => [self::ACTION_ACCESS, self::ACTION_DELETE],
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
//        self::ACT_CREATE    =>  [
//        ],
        self::OBJ_ORDER_ITEM    =>  [
            self::ATTR_ORDER_N,
            self::ATTR_ORDER_PROFIT_AMOUNT,
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
        $attrStatus = self::ATTR_ORDER_STATUS;
        $attrStatus['values'] = OrderStatus::getList();

        $attributeList = self::OBJECT_ATTRIBUTE_LIST;
        $attributeList[self::OBJ_ORDER_ITEM][] = $attrStatus;
        $attributeList[self::ACT_COMPLETE][] = $attrStatus;

        //$attributeList[self::OBJ_ORDER_ITEM][] = self::ATTR_ORDER_PROFIT_AMOUNT;

        return $attributeList;
    }
}
