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
     * @return string
     */
    public function getType(): string;

    /**
     * @param Query $query
     * @return Query
     */
    public function modifyQuery(Query $query): Query;
}
