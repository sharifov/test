<?php

namespace modules\eventManager\src\entities;

/**
 * This is the ActiveQuery class for [[EventHandler]].
 *
 * @see EventApp
 */
class EventHandlerScopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EventHandler[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EventHandler|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
