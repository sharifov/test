<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 9:42 PM
 */

namespace modules\requestControl\interfaces;


use yii\db\Query;

interface ConditionInterface
{
    /**
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query;
}