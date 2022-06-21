<?php

namespace modules\objectSegment\src\entities;

/**
 * This is the ActiveQuery class for [[ObjectSegmentTask]].
 *
 * @see ObjectSegmentTask
 */
class ObjectSegmentTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ObjectSegmentTask[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ObjectSegmentTask|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
