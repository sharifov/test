<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 9:22 PM
 */

namespace modules\requestControl\accessCheck\conditions;


use yii\db\Query;

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
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query
    {
        return $query->orWhere(["type" => self::TYPE, "subject" => $this->value]);
    }
}