<?php

namespace modules\requestControl\accessCheck\conditions;

/**
 * Class specify arguments and methods for using them in logic of access check
 *
 * Class AccessCheckCondition
 * @package modules\requestControl
 */
class RoleCondition extends AbstractCondition
{
    const TYPE = 'ROLE';

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::TYPE;
    }
}
