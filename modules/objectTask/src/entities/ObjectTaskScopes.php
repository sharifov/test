<?php

namespace modules\objectTask\src\entities;

/**
 * This is the ActiveQuery class for [[ObjectTask]].
 *
 * @see ObjectTask
 */
class ObjectTaskScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return ObjectTask[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTask|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
