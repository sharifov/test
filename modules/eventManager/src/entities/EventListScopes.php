<?php

namespace modules\eventManager\src\entities;

/**
 * This is the ActiveQuery class for [[EventList]].
 *
 * @see EventList
 */
class EventListScopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EventList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EventList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
