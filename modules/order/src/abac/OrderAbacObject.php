<?php

namespace modules\order\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;

class OrderAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'order/order/';

    /** --------------- PERMISSIONS --------------------------- */

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** ACTION PERMISSION */
    public const ACT_ALL     = self::NS . 'act/*';
    public const ACT_CREATE  = self::NS . 'act/create';
    public const ACT_READ    = self::NS . 'act/read';
    public const ACT_UPDATE  = self::NS . 'act/update';
    public const ACT_DELETE  = self::NS . 'act/delete';

    public const ACT_DETAIL_VIEW = self::NS . 'act/detail_view';
    public const ACT_START_AUTO_PROCESSING = self::NS . 'act/start_auto_processing';
    public const ACT_CANCEL_AUTO_PROCESSING = self::NS . 'act/cancel_auto_processing';
    public const ACT_CANCEL_ORDER = self::NS . 'act/cancel_order';
    public const ACT_SEND_EMAIL_CONFIRMATION = self::NS . 'act/send_email_confirmation';
    public const ACT_GENERATE_PDF = self::NS . 'act/generate_pdf';
    public const ACT_STATUS_LOG = self::NS . 'act/status_log';
    public const ACT_COMPLETE = self::NS . 'act/complete';

    /** UI PERMISSION */
    public const UI_ALL             = self::NS . 'ui/*';
    public const UI_BTN_CREATE      = self::NS . 'ui/btn/create';
    public const UI_BLOCK_PAYMENTS  = self::NS . 'ui/block/payments';
    public const UI_LINK_UPDATE     = self::NS . 'ui/link/update';
    public const UI_MENU_ACTIONS     = self::NS . 'ui/menu/actions';

    /** LOGIC PERMISSION */
    public const LOGIC_ALL         = self::NS . 'logic/*';
    public const LOGIC_SEND_MAIL   = self::NS . 'logic/send_mail';

    /** COMMAND PERMISSION */
    public const CMD_ALL                    = self::NS . 'cmd/*';
    public const CMD_RECALCULATE_PRICE      = self::NS . 'cmd/recalculate_price';

    /** OBJECT PERMISSION */
    public const OBJ_ALL            = self::NS . 'obj/*';
    public const OBJ_LEAD           = self::NS . 'obj/lead';
    public const OBJ_CASE           = self::NS . 'obj/case';
    public const OBJ_ORDER_ITEM     = self::NS . 'obj/order_item';



    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_ALL       => self::ACT_ALL,
        self::ACT_CREATE    => self::ACT_CREATE,
        self::ACT_READ      => self::ACT_READ,
        self::ACT_UPDATE    => self::ACT_UPDATE,
        self::ACT_DELETE    => self::ACT_DELETE,
        self::ACT_DETAIL_VIEW => self::ACT_DETAIL_VIEW,
        self::ACT_START_AUTO_PROCESSING => self::ACT_START_AUTO_PROCESSING,
        self::ACT_CANCEL_AUTO_PROCESSING => self::ACT_CANCEL_AUTO_PROCESSING,
        self::ACT_CANCEL_ORDER => self::ACT_CANCEL_ORDER,
        self::ACT_SEND_EMAIL_CONFIRMATION => self::ACT_SEND_EMAIL_CONFIRMATION,
        self::ACT_GENERATE_PDF => self::ACT_GENERATE_PDF,
        self::ACT_STATUS_LOG => self::ACT_STATUS_LOG,
        self::ACT_COMPLETE => self::ACT_COMPLETE,

        self::UI_ALL            => self::UI_ALL,
        self::UI_BTN_CREATE     => self::UI_BTN_CREATE,
        self::UI_BLOCK_PAYMENTS => self::UI_BLOCK_PAYMENTS,
        self::UI_LINK_UPDATE    => self::UI_LINK_UPDATE,
        self::UI_MENU_ACTIONS   => self::UI_MENU_ACTIONS,

        self::LOGIC_ALL            => self::LOGIC_ALL,
        self::LOGIC_SEND_MAIL      => self::LOGIC_SEND_MAIL,

        self::CMD_ALL                   => self::CMD_ALL,
        self::CMD_RECALCULATE_PRICE     => self::CMD_RECALCULATE_PRICE,

        self::OBJ_ALL       => self::OBJ_ALL,
        self::OBJ_LEAD      => self::OBJ_LEAD,
        self::OBJ_CASE      => self::OBJ_CASE,
        self::OBJ_ORDER_ITEM      => self::OBJ_ORDER_ITEM,
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS  = 'access';
    public const ACTION_CREATE  = 'create';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_DELETE  = 'delete';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ACT_ALL       => [self::ACTION_ACCESS, self::ACTION_CREATE,
            self::ACTION_READ, self::ACTION_UPDATE, self::ACTION_DELETE],
        self::ACT_CREATE    => [self::ACTION_ACCESS, self::ACTION_CREATE],
        self::ACT_READ      => [self::ACTION_ACCESS, self::ACTION_READ],
        self::ACT_UPDATE    => [self::ACTION_ACCESS, self::ACTION_UPDATE],
        self::ACT_DELETE    => [self::ACTION_ACCESS, self::ACTION_DELETE],
        self::OBJ_LEAD      => [self::ACTION_ACCESS, self::ACTION_READ],
        self::OBJ_ORDER_ITEM    => [self::ACTION_CREATE, self::ACTION_READ, self::ACTION_UPDATE, self::ACTION_DELETE],
        self::UI_BLOCK_PAYMENTS      => [self::ACTION_ACCESS, self::ACTION_READ],
        self::ACT_DETAIL_VIEW => [self::ACTION_ACCESS],
        self::ACT_START_AUTO_PROCESSING => [self::ACTION_ACCESS],
        self::ACT_CANCEL_AUTO_PROCESSING => [self::ACTION_ACCESS],
        self::ACT_CANCEL_ORDER => [self::ACTION_ACCESS],
        self::ACT_SEND_EMAIL_CONFIRMATION => [self::ACTION_ACCESS],
        self::ACT_GENERATE_PDF => [self::ACTION_ACCESS],
        self::ACT_STATUS_LOG => [self::ACTION_ACCESS],
        self::ACT_COMPLETE => [self::ACTION_ACCESS],
    ];


    protected const ATTR_ORDER_STATUS = [
        'optgroup' => 'Order',
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

    protected const ATTR_ORDER_PROFIT_AMOUNT = [
            'id' => self::NS . 'profit_amount',
            'field' => 'profit_amount',
            'label' => 'Profit amount',
            'type' => self::ATTR_TYPE_DOUBLE,
            'input' => self::ATTR_INPUT_NUMBER,
            'validation' => ['step' => 0.01],
            'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
    ];

    protected const ATTR_ORDER_N = [
            'id' => self::NS . 'n',
            'field' => 'n',
            'label' => 'N',
            'type' => self::ATTR_TYPE_INTEGER,
            'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
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
