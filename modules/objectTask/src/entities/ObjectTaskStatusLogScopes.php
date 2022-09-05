<?php

namespace modules\objectTask\src\entities;

/**
 * This is the ActiveQuery class for [[ObjectTaskStatusLog]].
 *
 * @see ObjectTaskStatusLog
 */
class ObjectTaskStatusLogScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ObjectTaskStatusLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskStatusLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
