<?php

namespace modules\order\src\abac;

class OrderAbacPolicy
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
}
