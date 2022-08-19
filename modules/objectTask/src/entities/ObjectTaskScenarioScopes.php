<?php

namespace modules\objectTask\src\entities;

/**
 * This is the ActiveQuery class for [[ObjectTaskScenario]].
 *
 * @see ObjectTaskScenario
 */
class ObjectTaskScenarioScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ObjectTaskScenario[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskScenario|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
