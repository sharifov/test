<?php

namespace modules\lead\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class LeadSearchAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'lead/search/';

    /** OBJECT PERMISSION */
    public const SIMPLE_SEARCH = self::NS . 'simple_search';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::SIMPLE_SEARCH => self::SIMPLE_SEARCH,
    ];

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::SIMPLE_SEARCH => [self::ACTION_ACCESS],
    ];

    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [];

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
