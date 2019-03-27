<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Sources]].
 *
 * @see Sources
 */
class SourcesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Sources[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Sources|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
