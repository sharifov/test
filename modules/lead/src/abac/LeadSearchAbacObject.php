<?php

namespace modules\lead\src\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class LeadSearchAbacObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    public const NS = 'lead/lead/';

    /** OBJECT PERMISSION */
    public const ADVANCED_SEARCH = self::NS . 'obj/advanced_search';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_ACCESS = 'access';


    /** --------------- QUERIES --------------------------- */
    public const QUERY_SHOW_IS_TEST = self::NS . 'query/show_is_test';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::ADVANCED_SEARCH => self::ADVANCED_SEARCH,
        self::QUERY_SHOW_IS_TEST => self::QUERY_SHOW_IS_TEST,
    ];

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::ADVANCED_SEARCH => [self::ACTION_ACCESS],
        self::QUERY_SHOW_IS_TEST => [self::ACTION_ACCESS],
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
