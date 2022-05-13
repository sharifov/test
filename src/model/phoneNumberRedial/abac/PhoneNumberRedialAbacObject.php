<?php

namespace src\model\phoneNumberRedial\abac;

use common\models\Lead;
use common\models\Quote;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class PhoneNumberRedialAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'phone-number-redial/phone-number-redial/';

    public const OBJ_PHONE_NUMBER_REDIAL =  self::NS . 'obj/phone-number-redial';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_MULTIPLE_DELETE  = 'multiple-delete';


    public const OBJECT_LIST = [
        self::OBJ_PHONE_NUMBER_REDIAL => self::OBJ_PHONE_NUMBER_REDIAL,
    ];

    public const OBJECT_ACTION_LIST = [
        self::OBJ_PHONE_NUMBER_REDIAL => [ self::ACTION_MULTIPLE_DELETE ],
    ];

    public const OBJECT_ATTRIBUTE_LIST = [
        self::OBJ_PHONE_NUMBER_REDIAL => [
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
        return self::OBJECT_ATTRIBUTE_LIST;
    }
}
