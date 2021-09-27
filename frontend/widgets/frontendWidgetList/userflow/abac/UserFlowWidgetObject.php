<?php

namespace frontend\widgets\frontendWidgetList\userflow\abac;

use modules\abac\components\AbacBaseModel;
use modules\abac\src\entities\AbacInterface;

class UserFlowWidgetObject extends AbacBaseModel implements AbacInterface
{
    /** NAMESPACE */
    private const NS = 'frontend/widgets/';

    /** UI PERMISSION */
    public const UI_INCLUDE_WIDGET      = self::NS . 'ui/user-flow-widget';

    /** --------------- OBJECT LIST --------------------------- */
    public const OBJECT_LIST = [
        self::UI_INCLUDE_WIDGET       => self::UI_INCLUDE_WIDGET
    ];

    /** --------------- ACTIONS --------------------------- */
    public const ACTION_INCLUDE  = 'include';

    /** --------------- ACTION LIST --------------------------- */
    public const OBJECT_ACTION_LIST = [
        self::UI_INCLUDE_WIDGET       => [self::ACTION_INCLUDE]
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
