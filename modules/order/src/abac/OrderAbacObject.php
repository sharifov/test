<?php

namespace modules\order\src\abac;

use modules\abac\src\entities\AbacInterface;

class OrderAbacObject implements AbacInterface
{

    private const NS = 'order/order/';

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


//    public const ACT_OBJECT_LIST = [
//        self::ACT_ALL       => self::ACT_ALL,
//        self::ACT_CREATE    => self::ACT_CREATE,
//        self::ACT_READ      => self::ACT_READ,
//        self::ACT_UPDATE    => self::ACT_UPDATE,
//        self::ACT_DELETE    => self::ACT_DELETE,
//    ];
//
//    public const UI_OBJECT_LIST = [
//        self::UI_ALL            => self::UI_ALL,
//        self::UI_BTN_CREATE     => self::UI_BTN_CREATE,
//        self::UI_BLOCK_PAYMENTS => self::UI_BLOCK_PAYMENTS,
//        self::UI_LINK_UPDATE    => self::UI_LINK_UPDATE,
//        self::UI_MENU_ACTIONS   => self::UI_MENU_ACTIONS,
//    ];

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


    public const ATTR_TYPE_INTEGER  = 'integer';
    public const ATTR_TYPE_STRING   = 'string';
    public const ATTR_TYPE_DOUBLE   = 'double';

    public const ATTR_INPUT_RADIO       = 'radio';
    public const ATTR_INPUT_CHECKBOX    = 'checkbox';
    public const ATTR_INPUT_SELECT      = 'select';

    public const ATTR_OPERATOR_EQUAL = 'equal';

/*'equal',
'not_equal',
'in',
'not_in',
'less',
'less_or_equal',
'greater',
'greater_or_equal',
'between',
'not_between',
'begins_with',
'not_begins_with',
'contains',
'not_contains',
'ends_with',
'not_ends_with',
'is_empty',
'is_not_empty',
'is_null',
'is_not_null',*/

    public const OBJECT_ATTRIBUTE_LIST = [
        self::ACT_CREATE    =>  [
            [
                'id' => 'id1',
                'label' => 'ID1',
                'type'  => "integer",
                //'value' => true // boolean
                'input' => 'radio',
                'values' => [
                    1 => 'Yes',
                    0 => 'No'
                ],
                'default_value' => 1,
                'operators' =>  ['equal'],
                'unique' => true,
                'description' => 'This filter is "unique", it can be used only once',
                'icon' => 'fa fa-ticket',
            ],

            [
                'id' => 'id2',
                'label' => 'ID2',
                'type' => 'string',
                'operators' =>  ['equal', 'not_equal', 'in', 'not_in', '==', '!=', 'match']
            ],
        ]
    ];

    /**
     * @return string[]
     */
    public static function getObjectList(): array
    {
        return self::OBJECT_LIST;
    }

//    public function getActionListByObject($object): array
//    {
//
//    }
//
//    public function getAttributeListByObject($object): array
//    {
//
//    }
}
