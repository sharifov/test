<?php

namespace modules\requestControl\accessCheck\conditions;

use yii\db\Query;

/**
 * Class specify arguments and methods for using them in logic of access check
 *
 * Class AccessCheckCondition
 * @package modules\requestControl
 */
class UsernameCondition extends AbstractCondition
{
    const TYPE = 'USERNAME';

    /**
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query
    {
        return $query->orWhere(["rcr_type" => self::TYPE, "rcr_subject" => $this->value]);
    }
}
