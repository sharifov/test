<?php

namespace modules\order\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

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

    /** UI PERMISSION */
    public const UI_ALL             = self::NS . 'ui/*';
    public const UI_BTN_CREATE      = self::NS . 'ui/btn/create';
    public const UI_BLOCK_PAYMENTS  = self::NS . 'ui/block/payments';
    public const UI_LINK_UPDATE     = self::NS . 'ui/link/update';
    public const UI_MENU_ACTIONS     = self::NS . 'ui/menu/actions';

    /** LOGIC PERMISSION */
    public const LOGIC_ALL         = self::NS . 'logic/*';
    public const LOGIC_SEND_MAIL   = self::NS . 'logic/send_mail';

    /** LOGIC PERMISSION */
    public const CMD_ALL                    = self::NS . 'cmd/*';
    public const CMD_RECALCULATE_PRICE      = self::NS . 'cmd/recalculate_price';

    /** OBJECT PERMISSION */
    public const OBJ_ALL            = self::NS . 'obj/*';
    public const OBJ_LEAD           = self::NS . 'obj/lead';
    public const OBJ_CASE           = self::NS . 'obj/case';



    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ACT_ALL       => self::ACT_ALL,
        self::ACT_CREATE    => self::ACT_CREATE,
        self::ACT_READ      => self::ACT_READ,
        self::ACT_UPDATE    => self::ACT_UPDATE,
        self::ACT_DELETE    => self::ACT_DELETE,

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
    ];


    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_CREATE    =>  [
            [
                'id' => self::NS . 'test1',
                'field' => 'test1',
                'label' => 'Test Attr1',
                'type'  => self::ATTR_TYPE_INTEGER,
                //'value' => true // boolean
                'input' => 'radio',
                'values' => [

                    1 => 'Yes',
                    0 => 'No'
                ],
                'default_value' => 1,
                'operators' =>  [self::OP_EQUAL],
                'unique' => true,
                'description' => 'This filter is "unique", it can be used only once',
                'icon' => 'fa fa-ticket',
            ],

            [
                'id' => self::NS . 'test2',
                'field' => 'test2',
                'label' => 'Test Attr2',
                'type' => 'string',
                'operators' =>  [self::OP_EQUAL, self::OP_NOT_EQUAL, self::OP_IN,
                    self::OP_NOT_IN, self::OP_EQUAL2, self::OP_NOT_EQUAL2, self::OP_MATCH]
            ],
        ],

        self::OBJ_LEAD    =>  [
            [
                'id' => self::NS . 'n',
                'field' => 'n',
                'label' => 'N',
                'type' => self::ATTR_TYPE_INTEGER,
                'operators' =>  [self::OP_EQUAL2, self::OP_NOT_EQUAL2,
                    self::OP_IN, self::OP_NOT_IN, '<', '>', '<=', '>=']
            ],
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
