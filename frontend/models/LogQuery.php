<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Log]].
 *
 * @see Log
 */
class LogQuery extends \yii\db\ActiveQuery
{
    /**
     * @inheritdoc
     * @return Log[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Log|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}