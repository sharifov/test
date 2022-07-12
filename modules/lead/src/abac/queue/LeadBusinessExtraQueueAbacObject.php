<?php

namespace modules\lead\src\abac\queue;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;
use modules\lead\src\abac\LeadAbacObject;

/**
 * Class LeadBusinessExtraQueueAbacObject
 */
class LeadBusinessExtraQueueAbacObject extends AbacBaseModel implements AbacInterface
{
    public const NS = LeadAbacObject::NS . 'business_extra_queue/';

    /** ALL PERMISSIONS */
    public const ALL = self::NS . '*';

    /** UI PERMISSION */
    public const UI_ACCESS  = self::NS . 'ui/access';

    /** ACTIONS */
    public const ACTION_ACCESS = 'access';
    public const ACTION_TAKE = 'take';

    public static function getObjectList(): array
    {
        return [
            self::UI_ACCESS => self::UI_ACCESS,
        ];
    }

    public static function getObjectActionList(): array
    {
        return [
            self::UI_ACCESS => [self::ACTION_ACCESS, self::ACTION_TAKE],
        ];
    }

    public static function getObjectAttributeList(): array
    {
        return [];
    }
}
