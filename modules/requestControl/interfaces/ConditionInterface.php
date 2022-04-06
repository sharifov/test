<?php

namespace modules\requestControl\interfaces;

use yii\db\Query;

/**
 * Interface ConditionInterface
 * @package modules\requestControl\interfaces
 */
interface ConditionInterface
{
    /**
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query;
}
