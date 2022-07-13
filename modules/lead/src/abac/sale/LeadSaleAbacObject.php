<?php

namespace modules\lead\src\abac\sale;

use common\models\Lead;
use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;

class LeadSaleAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'sale';

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_VIEW = 'view';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::NS => self::NS,
    ];

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::NS => [self::ACTION_VIEW],
    ];
    /** --------------- ATTRIBUTE LIST --------------------------- */
    public const OBJECT_ATTRIBUTE_LIST = [
        self::NS => [
            LeadAbacObject::ATTR_LEAD_IS_OWNER,
            LeadAbacObject::ATTR_LEAD_HAS_OWNER,
            LeadAbacObject::ATTR_IS_IN_PROJECT,
            LeadAbacObject::ATTR_IS_IN_DEPARTMENT,
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
        $attributes = self::OBJECT_ATTRIBUTE_LIST;

        $leadStatuses = Lead::getAllStatuses();
        $attrStatus = LeadAbacObject::ATTR_LEAD_STATUS;
        $attrStatus['values'] = $leadStatuses;
        $attributes[self::NS][] = $attrStatus;
        return $attributes;
    }
}
